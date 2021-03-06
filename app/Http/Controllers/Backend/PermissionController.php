<?php

namespace Futbol\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Futbol\Http\Controllers\Controller;
use Futbol\RepositoryInterface\PermissionRepositoryInterface;
use Futbol\RepositoryInterface\PermissionTypeRepositoryInterface;
use Futbol\RepositoryInterface\ModuleRepositoryInterface;
use Futbol\Http\Requests\PermissionRequest;
use Futbol\Exceptions\PermissionException;
use Exception;
use DB;

class PermissionController extends Controller
{
    
    protected $permission;

    protected $permissionType;

    protected $module;

    protected $routeRedirectIndex = 'permissions.index';


    function __construct(PermissionRepositoryInterface $permission, PermissionTypeRepositoryInterface $permissionType, ModuleRepositoryInterface $module)
    {
        $this->middleware('auth');
        $this->permission = $permission;
        $this->permissionType = $permissionType;
        $this->module = $module;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = $this->permission->enum();
        return view('backend.permission.index',compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $paramsPTypes = [
            'state' => $this->permissionType->getModel()->getActive()
        ];
        
        $permissionTypes = $this->permissionType->enum($paramsPTypes);
        
        $paramsModule = [
            'state' => $this->module->getModel()->getActive()
        ];

        $modules = $this->module->enum($paramsModule);
        
        $parents = $this->permission->enum();
        return view('backend.permission.create-edit',compact('permissionTypes','modules','parents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request)
    {
        $message = [
            'type' => 'success',
            'content' =>'',
        ];

        DB::beginTransaction();
 

        try {
            $message['content'] = "Se ha creado el permiso Satisfactoriamente";
            $permission = $this->permission->save($request->all());
            DB::commit();
            if ($request->get('redirect-index') == 1) {
                return redirect()->route($this->routeRedirectIndex)->with($message);
            } else {
                return redirect()->route('permissions.edit',['id'=>$permission->id])->with($message);
            }
        } catch (PermissionException $e) {
             DB::rollback();
            $message['type'] = "error";
            $message['content'] = $e->getMessage();
            return back()->with($message);
        }catch (Exception $e) {
            DB::rollback();
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
        
        $paramsPTypes = [
            'state' => $this->permissionType->getModel()->getActive()
        ];
        $permissionTypes = $this->permissionType->enum($paramsPTypes);
        
        $paramsModule = [
            'state' => $this->module->getModel()->getActive()
        ];
        $modules = $this->module->enum($paramsModule);
        $parents = $this->permission->enum();
        $permission = $this->permission->find($id);
        return view('backend.permission.create-edit',compact('permission','parents','modules','permissionTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionRequest $request, $id)
    {
        $message = [
            'type' => 'success',
            'content' =>'',
        ];
        DB::beginTransaction();
        try {
          $permission = $this->permission->edit($id,$request->all());
          DB::commit();
          $message['content'] = "Se ha Actualizado el permiso satisfactoriamente";
          
          if ($request->get('redirect-index') == 1) { 
            return redirect()->route($this->routeRedirectIndex)->with($message);
          } else {
            return back()->with($message);
          }
          
        } catch (PermissionException $e) {
            DB::rollback();
            $message['type'] = 'error';
            $message['content'] = $e->getMessage();
        }catch (Exception $e) {
            DB::rollback();
            $message['type'] = "error";
            $message['content'] = $e->getMessage();
            return back()->with($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PermissionRequest $request, $id)
    {
        $message = [
            'type' => 'success',
            'content' =>'',
        ];
        DB::beginTransaction();

        try {
            $deleted = $this->permission->remove($id);
            DB::commit();
            $message['content'] = "Se ha eliminado el permiso satisfactoriamente";
            return back()->with($message);
        } catch (PermissionException $e) {
            DB::rollback();
            $message['type'] = "error";
            $message['content'] = $e->getMessage();
            return back()->with($message);
        }catch (Exception $e) {
            DB::rollback();
            $message['type'] = "error";
            $message['content'] = $e->getMessage();
            return back()->with($message);
        }
    }
}
