<?php
namespace App\Services\Csv;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\DTOs\ClientDto;
use App\DTOs\LeadDTO;
use App\DTOs\TaskDTO;
use ReflectionClass;
use ReflectionProperty;

class CsvService
{
    public function importCsv($filePath, $className)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception("Could not open file: {$filePath}");
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new \Exception("Empty or invalid CSV file");
        }

        $results = new Collection();
        $errors = new Collection();
        $reflection = new ReflectionClass($className);
        $lineNumber = 2; 

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            $instance = new $className();
            $rowErrors = [];

            foreach ($data as $property => $value) {
                if ($reflection->hasProperty($property)) {
                    $reflectionProperty = $reflection->getProperty($property);
                    $reflectionProperty->setAccessible(true);

                    try {
                        $parsedValue = $this->parseValue($reflectionProperty, $value, $property, $filePath, $lineNumber, $rowErrors);
                        $reflectionProperty->setValue($instance, $parsedValue);
                    } catch (\Exception $e) {
                        $rowErrors[] = $e->getMessage();
                    }
                }
            }

            if (empty($rowErrors)) {
                $results->push($instance);
            } else {
                $errors->push([
                    'file' => basename($filePath),
                    'line' => $lineNumber,
                    'errors' => $rowErrors,
                ]);
            }

            $lineNumber++;
        }

        fclose($handle);
        return [$results, $errors];
    }

    private function parseValue($property, $value, $propertyName, $filePath, $lineNumber, &$rowErrors)
    {
        if (empty($value)) {
            return null;
        }

        $type = $this->getPropertyType($property);
        Log::info("Parsing value: {$value} for property: {$propertyName} with type: {$type}");

        if (($type === 'float' || $type === 'double' || $type === 'int' || $type === 'integer') && $value < 0) {
            throw new \Exception("Negative amount not allowed for '{$propertyName}' at line {$lineNumber}");
        }

        try {
            switch ($type) {
                case 'int':
                case 'integer':
                    return (int) $value;

                case 'float':
                case 'double':
                    return (float) $value;

                case 'bool':
                case 'boolean':
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);

                case 'array':
                    return array_map('trim', explode(',', $value));

                case 'datetime':
                    $date = Carbon::createFromFormat('Y-m-d H:i:s', $value);
                    if (!$date) {
                        throw new \Exception("Invalid datetime format for '{$propertyName}' at line {$lineNumber}. Expected 'Y-m-d H:i:s'");
                    }
                    return $date;

                case 'date':
                    $date = Carbon::createFromFormat('d/m/Y', $value);
                    if (!$date) {
                        throw new \Exception("Invalid date format for '{$propertyName}' at line {$lineNumber}. Expected 'd/m/Y'");
                    }
                    return $date->startOfDay();

                case 'time':
                    $time = Carbon::createFromFormat('H:i', $value);
                    if (!$time) {
                        throw new \Exception("Invalid time format for '{$propertyName}' at line {$lineNumber}. Expected 'H:i'");
                    }
                    return $time;

                default:
                    if ($propertyName === 'status' && !in_array($value, ['new', 'in_progress', 'completed', 'pending'])) {
                        throw new \Exception("Invalid status '{$value}' for '{$propertyName}' at line {$lineNumber}. Allowed: new, in_progress, completed, pending");
                    }
                    return $value;
            }
        } catch (\Exception $e) {
            Log::warning("Error parsing value: {$value} for property: {$propertyName}", [
                'exception' => $e->getMessage(),
                'file' => $filePath,
                'line' => $lineNumber,
            ]);
            throw $e;
        }
    }

    private function getPropertyType($property)
    {
        if (PHP_VERSION_ID >= 70400 && $property->hasType()) {
            $type = $property->getType();
            return $type->getName();
        }

        $docComment = $property->getDocComment();
        if ($docComment) {
            if (preg_match('/@var\s+([^\s]+)/', $docComment, $matches)) {
                return strtolower($matches[1]);
            }
        }

        return 'string';
    }
}