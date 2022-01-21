<?php

namespace App\Orchid\Screens;

use App\Orchid\Layouts\ProxyListLayout;
use App\Models\Proxy;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class ProxyListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Proxy';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'All proxies';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'proxy' => Proxy::paginate()
        ];
    }

    /**
     * Button commands.
     *
     * @return Link[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Create new')
                ->icon('pencil')
                ->route('platform.proxy.edit')
        ];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            ProxyListLayout::class
        ];
    }
}
