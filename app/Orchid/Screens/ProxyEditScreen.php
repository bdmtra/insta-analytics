<?php

namespace App\Orchid\Screens;

use App\Models\Proxy;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class ProxyEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Creating a new proxy';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = '';

    /**
     * @var bool
     */
    public $exists = false;

    /**
     * Query data.
     *
     * @param Proxy $proxy
     *
     * @return array
     */
    public function query(Proxy $proxy): array
    {
        $this->exists = $proxy->exists;

        if($this->exists){
            $this->name = 'Edit proxy';
        }

        return [
            'proxy' => $proxy
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
            Button::make('Create proxy')
                ->icon('plus')
                ->method('createOrUpdate')
                ->canSee(!$this->exists),

            Button::make('Update')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->exists),

            Button::make('Remove')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->exists),
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
            Layout::rows([
                Input::make('proxy.uri')
                    ->title('URI')
            ])
        ];
    }

    /**
     * @param Proxy    $proxy
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Proxy $proxy, Request $request)
    {
        $proxy->fill($request->get('proxy'))->save();

        Alert::info('You have successfully created an proxy.');

        return redirect()->route('platform.proxy.list');
    }

    /**
     * @param Proxy $proxy
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Proxy $proxy)
    {
        $proxy->delete();

        Alert::info('You have successfully deleted the proxy.');

        return redirect()->route('platform.proxy.list');
    }
}
