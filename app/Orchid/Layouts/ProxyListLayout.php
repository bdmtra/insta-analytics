<?php

namespace App\Orchid\Layouts;

use App\Models\Proxy;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;

class ProxyListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'proxy';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('uri', 'URI')
                ->render(function (Proxy $proxy) {
                    return Link::make($proxy->uri)
                        ->route('platform.proxy.edit', $proxy);
                }),
        ];
    }
}
