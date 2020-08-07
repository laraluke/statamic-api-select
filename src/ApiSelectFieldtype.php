<?php

namespace Laraluke\ApiSelectFieldtype;

use Statamic\Fields\Fieldtype;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class ApiSelectFieldtype extends Fieldtype
{
    protected static $title = 'API Select';

    protected $icon = 'select';
    
    protected function configFieldItems(): array
    {
        return [
            'placeholder' => [
                'display' => __('Placeholder'),
                'instructions' => __('statamic::fieldtypes.select.config.placeholder'),
                'type' => 'text',
                'default' => '',
                'width' => 50,
            ],
            'endpoint_type' => [
                'display' => __('Endpoint Type'),
                'type' => 'select',
                'options' => [
                    'config' => 'Config',
                    'url' => 'URL',
                ],
                'default' => 'url',
                'width' => 25,
                'required' => true,
            ],
            'endpoint' => [
                'display' => __('Endpoint'),
                'type' => 'text',
                'placeholder' => __('URL / Config "dot" syntax variable.'),
                'width' => 75,
                'required' => true,
            ],
            'cache_minutes' => [
                'display' => __('Cache Duration'),
                'instructions' => __('How long API results should be cached for in minutes.'),
                'type' => 'text',
                'input_type' => 'number',
                'default' => 0,
                'width' => 25,
            ],
            'data_set_key' => [
                'display' => __('Data Set Key'),
                'instructions' => __('If your data set isn\'t top-level, you can define it\'s location.'),
                'type' => 'text',
                'placeholder' => 'data.users',
                'width' => 25,
            ],
            'item_key' => [
                'display' => __('Item Key'),
                'instructions' => __('Define the unique identifier to be used as the option value.'),
                'type' => 'text',
                'placeholder' => 'id',
                'width' => 25,
                'required' => true,
            ],
            'item_label' => [
                'display' => __('Item Label'),
                'instructions' => __('Define the value to be used as the option label.'),
                'type' => 'text',
                'placeholder' => 'name',
                'width' => 25,
                'required' => true,
            ],
            'clearable' => [
                'display' => __('Clearable'),
                'instructions' => __('statamic::fieldtypes.select.config.clearable'),
                'type' => 'toggle',
                'default' => false,
                'width' => 25,
            ],
            'multiple' => [
                'display' => __('Multiple'),
                'instructions' => __('statamic::fieldtypes.select.config.multiple'),
                'type' => 'toggle',
                'default' => false,
                'width' => 25,
            ],
            'searchable' => [
                'display' => __('Searchable'),
                'instructions' => __('statamic::fieldtypes.select.config.searchable'),
                'type' => 'toggle',
                'default' => true,
                'width' => 25,
            ],
            'cast_booleans' => [
                'display' => __('Cast Booleans'),
                'instructions' => __('statamic::fieldtypes.select.config.cast_booleans'),
                'type' => 'toggle',
                'default' => false,
                'width' => 25,
            ],
        ];
    }

    public function preload()
    {
        return [
            'options' => $this->getOptions(),
        ];
    }

    public function augment($value)
    {
        $data = collect($this->getData());
        $key = $this->config('item_key');

        $values = $data
            ->whereIn($key, $value);

        if (!is_array($value)) {
            return $values->first();
        }

        return $values->all();
    }

    public function preProcess($value)
    {
        if ($this->config('cast_booleans')) {
            if ($value === true) {
                return 'true';
            } elseif ($value === false) {
                return 'false';
            }
        }

        return $value;
    }

    public function preProcessIndex($value)
    {
        $data = collect($this->getData());
        $key = $this->config('item_key');
        $label = $this->config('item_label');

        return $data
            ->whereIn($key, $value)
            ->implode($label, ', ');
    }

    public function process($value)
    {
        if ($this->config('cast_booleans')) {
            if ($value === 'true') {
                return true;
            } elseif ($value === 'false') {
                return false;
            }
        }

        return $value;
    }

    private function getOptions()
    {
        $data = $this->getData();
        $dsKey = $this->config('data_set_key');

        return collect(data_get($data, $dsKey))
            ->mapWithKeys(function ($option) {
                $key = $this->config('item_key');
                $label = $this->config('item_label');

                return [ data_get($option, $key) => data_get($option, $label) ];
            })
            ->all();
    }

    private function getData()
    {
        $key = $this->handle() . $this->config('endpoint');
        $minutes = $this->config('cache_minutes');

        if (!$data = Cache::get($key)) {
            $response = app(Client::class)->get($this->getEndpoint());

            $data = json_decode((string) $response->getBody(), true);

            if ($minutes > 0) {
                Cache::put($key, $data, now()->addMinutes($minutes));
            }
        }

        return $data;
    }

    private function getEndpoint()
    {
        $endpoint = $this->config('endpoint');

        switch ($this->config('endpoint_type')) {
            case 'config':
                return config($endpoint);
            default:
                return $endpoint;
        }
    }
}
