<?php

namespace HappyFeet\Http\Controllers\Backend;

use Illuminate\Http\Request;
use HappyFeet\Http\Controllers\Controller;
use HappyFeet\Http\Requests\ModuleRequest;
use HappyFeet\RepositoryInterface\ModuleRepositoryInterface;
use HappyFeet\Exceptions\ModuleException;


class ModuleController extends Controller
{
    

    protected $module;

    protected $routeRedirectIndex = 'modules.index';


    public function __construct(ModuleRepositoryInterface $module)
    {
        $this->middleware('auth');
        $this->module = $module;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $modules = $this->module->paginate();
        return view('backend.module.index',compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.module.create-edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ModuleRequest $request)
    {
        $message = [
            'type' => 'primary',
            'content' =>'',
        ];

        try {
            $message['content'] = "Se ha creado el módulo satisfactoriamente";
            $module = $this->module->save($request->all());
            if ($request->get('redirect-index') == 1) {
                return redirect()->route($this->routeRedirectIndex)->with($message);
            } else {
                return redirect()->route('modules.edit',['id'=>$module->id])->with($message);
            }
        } catch (ModuleException $e) {
            $message['type'] = "error";
            $message['content'] = $e->getMessage();
            return back()->with($message);
        }
            
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $module = $this->module->find($id);
        return view('backend.module.create-edit',compact('module'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message = [
            'type' => 'primary',
            'content' =>'',
        ];
        try {
          $module = $this->module->edit($id,$request->all());
          $message['content'] = "Se ha Actualizado el módulo satisfactoriamente";
          
          if ($request->get('redirect-index') == 1) { 
            return redirect()->route($this->routeRedirectIndex)->with($message);
          } else {
            return back()->with($message);
          }
          
        } catch (ModuleException $e) {
            $message['type'] = 'error';
            $message['content'] = $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = [
            'type' => 'primary',
            'content' =>'',
        ];
        try {
            $deleted = $this->module->remove($id);
            $message['content'] = "Se ha eliminado el módulo satisfactoriamente";
            return back()->with($message);
        } catch (ModuleException $e) {
            $message['type'] = "error";
            $message['content'] = $e->getMessage();
            return back()->with($message);
        }
        
    }
}
