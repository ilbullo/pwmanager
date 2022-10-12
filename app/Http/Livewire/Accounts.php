<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Accounts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = "";

    protected $model = \App\Models\Account::class;

    public $updateMode = false;

    //form elements
    public $form = [
        'account_id'    => '',
        'website'       => '',
        'username'      => '',
        'password'      => '',
        'note'          => ''
    ];

    //various messages
    public $deleteMessage = 'Account Deleted Successfully';
    public $updateMessage = 'Account Updated Successfully';
    public $createMessage = 'Account Created Successfully';
    public $restoreMessage = 'Account Restored Successfully';
    public $errorMessage  = 'Something wrong on request';
    public $createButtonLabel = 'Create Account';
    public $createModalTitle = 'New Account';
    public $updateModalTitle = 'Update Account';

    //show or hide trashed elements
    public $showTrashed  = true;



    /***********************************************************
     * Render component as a list of elements
     * @return View
     **********************************************************/

    public function render()
    {
        $items = $this->showTrashed ? $this->model::withTrashed() : $this->model::orderBy('website','ASC');

        if ($this->search !='') {
            $items->where('website','like','%' . $this->search . '%')
                  ->orWhere('username','like','%' . $this->search . '%')
                  ->orWhere('note','like','%' . $this->search . '%');
        }

        return view('livewire.accounts.home', ['items' => $items->paginate(15)])->extends('layouts.layout');
    }

    /***********************************************************
     * Show modal and form for edit an element
     * @param Int $id
     * @return void
     **********************************************************/

    public function edit($id)
    {
        $this->updateMode = true;
        $account = $this->model::where('id', $id)->first();
        $this->form['account_id']   = $id;
        $this->form['website']      = $account->website;
        $this->form['username']     = $account->username;
        $this->form['password']     = $account->password;
        $this->form['note']         = $account->note;

    }

    /***********************************************************
     * Reset input fields and close modal
     * @return void
     **********************************************************/

    public function cancel()
    {
        $this->updateMode = false;
        $this->resetInputFields();
    }

    /**********************************************************
     * Store a new element on DB after validation
     * @return void
     **********************************************************/

    public function store()
    {

        //validate form
        $this->validate([
            'form.website'     => 'required|string',
            'form.username'    => 'required|string',
            'form.password'    => 'required|string'
        ]);

        $item = $this->model::create([
            'website'   => $this->form['website'],
            'username'  => $this->form['username'],
            'password'  => $this->form['password'],
            'note'      => $this->form['note'],
        ]);

        session()->flash('message', __($this->createMessage));
        session()->flash('type', 'success');

        //reset fields
        $this->resetInputFields();
        $this->emit('AccountStore');
    }

    /***********************************************************
     * Update an element after validation pass
     * @return void
     **********************************************************/

    public function update()
    {
        $this->validate([
            'form.website'     => 'required|string',
            'form.username'    => 'required|string',
            'form.password'    => 'required|string'
        ]);

        if ($this->form['account_id']) {

            $account = $this->model::find($this->form['account_id']);

            $account->update([
                'website'   => $this->form['website'],
                'username'  => $this->form['username'],
                'password'  => $this->form['password'],
                'note'      => $this->form['note'],
            ]);

            $this->updateMode = false;
            session()->flash('message', $this->updateMessage);
            session()->flash('type', 'success');

            $this->resetInputFields();
        }
    }

    /***********************************************************
     * Put in the bin an element
     * @param Int $id
     * @return void
     **********************************************************/

    public function delete($id)
    {
        if ($id) {

            try {
                $this->model::find($id)->delete();

                session()->flash('message', $this->deleteMessage);
                session()->flash('type', 'success');
            } catch (\Exception $e) {
                session()->flash('message', $this->errorMessage);
                session()->flash('type', 'danger');
                Log::error("Errore cancellazione agenzia: " . $e->getMessage());
            }
        }
    }

    /***********************************************************
     * Restore an element from trash
     * @param Int $id
     * @return void
     **********************************************************/

    public function restore($id)
    {
        $this->model::withTrashed()->find($id)->restore();
        session()->flash('message', __($this->restoreMessage));
        session()->flash('type', 'success');
    }

    /***********************************************************
     * Reset all input fields of the form
     **********************************************************/

    private function resetInputFields()
    {
        $this->form = \array_fill_keys(\array_keys($this->form), '');
    }
}
