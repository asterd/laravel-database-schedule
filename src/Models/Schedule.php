<?php

namespace RobersonFaria\DatabaseSchedule\Models;

use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Schedule extends Model
{
    use ManagesFrequencies;
    use SoftDeletes;

    public const SESSION_KEY_ORDER_BY = 'schedule_order_by';
    public const SESSION_KEY_DIRECTION = 'schedule_order_by_direction';
    public const SESSION_KEY_FILTERS = 'schedule_filters';

    public const STATUS_INACTIVE = '0';
    public const STATUS_ACTIVE = '1';
    public const STATUS_TRASHED = '2';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;
    protected $connection;

    protected $fillable = [
        'command',
        'command_custom',
        'params',
        'options',
        'expression',
        'even_in_maintenance_mode',
        'without_overlapping',
        'on_one_server',
        'webhook_before',
        'webhook_after',
        'email_output',
        'sendmail_error',
        'sendmail_success',
        'log_success',
        'log_error',
        'status',
        'run_in_background',
        'log_filename',
        'groups',
        'environments',
        'without_overlapping_expires_at',
    ];

    protected $attributes = [
        'expression' => '* * * * *',
        'params' => '{}',
        'options' => '{}',
    ];

    protected $casts = [
        'params' => 'array',
        'options' => 'array'
    ];

    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->connection = Config::get('database-schedule.connection', 'pgsql');
        $this->table = Config::get('database-schedule.table.schedules', 'schedules');
    }

    public function histories()
    {
        return $this->hasMany(ScheduleHistory::class, 'schedule_id', 'id');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getArguments(): array
    {
        $arguments = [];

        foreach (($this->params ?? []) as $argument => $value) {
            if (!is_array($value) || !array_key_exists('value', $value)) {
                continue;
            }
            if ($value['value'] === null || $value['value'] === '') {
                continue;
            }
            if (isset($value["type"]) && $value['type'] === 'function') {
                $arguments[$argument] = $this->evaluateDynamicValue($value['value']);
            } else {
                $arguments[$argument] = $value['value'];
            }
        }

        return $arguments;
    }

    public function getOptions(): array
    {
        $options = [];
        foreach (($this->options ?? []) as $option => $value) {
            if (is_array($value) && ($value['value'] ?? null) === null) {
                continue;
            }
            $option = '--' . $option;
            if (is_array($value)) {
                if (isset($value["type"]) && $value['type'] === 'function') {
                    $options[$option] = $this->evaluateDynamicValue($value['value']);
                } else {
                    $options[$option] = $value['value'];
                }
            } else {
                $options[] = $option;
            }
        }

        return $options;
    }

    public function getCommandParameters(): array
    {
        return array_merge(array_values($this->getArguments()), $this->getOptions());
    }

    private function evaluateDynamicValue($expression): string
    {
        if (!config('database-schedule.allow_dynamic_parameters', true)) {
            throw new \RuntimeException('Dynamic schedule parameters are disabled.');
        }

        return (string) eval('return ' . rtrim($expression, ';') . ';');
    }

    public static function getGroups()
    {
        return static::whereNotNull('groups')
            ->groupBy('groups')
            ->get('groups')
            ->pluck('groups', 'groups');
    }

    public static function getEnvironments()
    {
        return static::whereNotNull('environments')
            ->groupBy('environments')
            ->get('environments')
            ->pluck('environments', 'environments');
    }
}
