<div>
    <slot name="page_title">
        <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">{{__('Accounts')}}</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#accountModal">
                                {{ __($createButtonLabel)}} <i class="fa-regular fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
    </slot>
    @include('livewire.accounts.create')
    @include('livewire.accounts.update')
    @include('layouts.partials.alerts')
    <div class="row">
        <div class="col-4">
            <p>{{__('Filters')}}: <input type="checkbox" class="form-check-input" wire:model="showTrashed"> {{__('Show trashed')}}</p>
        </div>
        <div class="col-8">
            <input type="text" name="search" wire:model="search">
        </div>
    </div>
    <table class="table table-bordered mt-5">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __("Website")}}</th>
                <th>{{ __("Username")}}</th>
                <th>{{ __("Password")}}</th>
                <th>{{ __("Note")}}</th>
                <th>{{ __("Actions") }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $account)
                <tr>
                    <td>{{ $account->id }}</td>
                    <td>{{ $account->website }}</td>
                    <td>{{ $account->username }}</td>
                    <td>{{ $account->password }}</td>
                    <td>{{ $account->note }}</td>
                    <td>
                        @if ($account->trashed())
                            <button wire:click="restore({{ $account->id }})"
                                class="btn btn-outline-success btn-sm" title="{{ __('Restore') }}"><i class="fa-solid fa-trash-can-arrow-up"></i></button>
                        @else
                            <button data-bs-toggle="modal" data-bs-target="#updateModal"
                                wire:click="edit({{ $account->id }})" class="btn btn-outline-primary btn-sm" title="{{__('Edit')}}"><i class="fa-regular fa-pen-to-square"></i></button>
                            @include('livewire.partials.delete', ['id' => $account->id])
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links() }}
</div>
