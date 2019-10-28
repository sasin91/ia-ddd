<?php

namespace App\Collections;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use function class_basename;
use function class_exists;
use function is_subclass_of;
use function resolve;

class FileCollection extends Collection
{
    public function whereClassExists()
    {
        return $this->filter(function ($file) {
            return class_exists($file->class);
        });
    }

    public function whereSubclassOf(string $className)
    {
        return $this->filter(function ($file) use ($className) {
            return is_subclass_of($file->class, $className);
        });
    }

    public function whereClassName(string $className)
    {
        return $this->filter(function ($file) use ($className) {
            return $file->class === $className
                || class_basename($file->name) === Str::studly($className);
        });
    }

    public function mapToInstances()
    {
        return new Collection($this->map(function (string $className) {
            try {
                return resolve($className);
            } catch (Exception $e) {
                return null;
            }
        })->filter());
    }

}
