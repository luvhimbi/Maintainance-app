<?php

namespace App;

trait LogsHistory
{
    protected static function bootLogsHistory()
    {
        foreach (static::getModelEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->logChange($event);
            });
        }
    }

    protected static function getModelEvents()
    {
        return ['updated'];
    }

    public function logChange($action)
    {
        $changed = $this->getDirty();
        
        // Don't log if nothing changed
        if (empty($changed)) {
            return;
        }

        $original = Arr::only($this->getOriginal(), array_keys($changed));

        HistoryLog::create([
            'issue_id' => $this->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_values' => $original,
            'new_values' => $changed,
            'description' => $this->getDescriptionForEvent($action, $changed)
        ]);
    }

    protected function getDescriptionForEvent($eventName, $changes)
    {
        $description = "Issue was {$eventName}.";
        
        if ($eventName === 'updated') {
            $description = "Updated issue: ";
            foreach ($changes as $field => $value) {
                $description .= "{$field} changed to {$value}, ";
            }
            $description = rtrim($description, ', ');
        }
        
        return $description;
    }
}
