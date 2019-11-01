<?php

namespace App\Collections;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Nova;
use function class_basename;
use function class_exists;
use function class_uses_recursive;
use function in_array;
use function is_object;
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

    public function whereUsing(string $traitName)
    {
        return $this->filter(function ($file) use ($traitName) {
            return in_array($traitName, class_uses_recursive($file->class));
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

    public function mapToNovaResources()
    {
        return $this->map(function ($fileOrClassName) {
            return Nova::resourceForModel(
                is_object($fileOrClassName) ? $fileOrClassName->class : $fileOrClassName
            );
        })->filter();
    }

    public function mapToInstances()
    {
        return new Collection($this->map(function ($fileOrClassName) {
            try {
                return resolve(is_object($fileOrClassName) ? $fileOrClassName->class : $fileOrClassName);
            } catch (Exception $e) {
                return null;
            }
        })->filter());
    }

}
