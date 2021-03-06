<?php
namespace Futbol\Repository;

use Futbol\RepositoryInterface\ModuleRepositoryInterface;
use Futbol\Exceptions\ModuleException;
use Futbol\Models\Module;
use Futbol\Models\Permission;

/**
* 
*/
class ModuleRepository implements ModuleRepositoryInterface
{
	
	public function paginate()
	{
		return Module::paginate();
	}

	public function enum($params = null)
	{
		if ($params) {
			if (is_array($params)) {
				if (array_key_exists('state', $params)) {
					$modules = Module::with('permissions')->where('state',$params['state'])->orderBy('order')->get();
				} else {
					$modules = Module::with('permissions')->orderBy('order')->get();
				}
			}
		} else {
			$modules = Module::with('permissions')->orderBy('order')->get();
		}

		if (!$modules) {
			throw new ModuleException('No se han encontrado el listado de  módulos',404);
		}
		return $modules;
	}



	public function find($field)
	{
		if (is_array($field)) {

			if (array_key_exists('name', $field)) { 
				$Module = Module::where('name',$field['name'])->first();	
			} else {

				throw new ModuleException('No se puede buscar el módulo',404);	
			}

		} elseif (is_string($field) || is_int($field)) {
			$Module = Module::where('id',$field)->first();
		} else {
			throw new ModuleException('Se ha producido un error al buscar el módulo',500);	
		}

		if (!$Module) throw new ModuleException('No se puede buscar al módulo',404);	
		
		return $Module;

	}

	//TODO
	public function save($data)
	{
		$module = new Module();
		$module->fill($data);
		if ($module->save()) {
			$key = $module->getKey();
			return  $this->find($key);
		} else {
			throw new ModuleException('Ha ocurrido un error al guardar el módulo '.$data['name'],500);
		}		
	}

	public function edit($id,$data)
	{
		$module = Module::find($id);
		if ($module) {
			$module->fill($data);
			if($module->update()){
				$key = $module->getKey();
				return $this->find($key);
			}
		} else {
			throw new ModuleException('Ha ocurrido un error al actualizar el módulo '.$data['name'],500);
		}


	}

	public function remove($id)
	{
		if ($module = $this->find($id)) {
			$module->delete();
			return true;
		}
		throw new ModuleException('Ha ocurrido un error al eliminar el módulo ',500);
	}

	public function getModel()
	{
		return new Module();
	}


	public function loadMenu($userId)
	{
		// $query  = Module::select('module.*')->with(['permissions'=>function($query) use($userId){
		// 				$query->selectRaw('distinct permission.*')
		// 				->leftJoin('permission_role as rPer','rPer.permission_id','=','permission.id')
		// 				->leftJoin('user_role as rolU','rolU.role_id','=','rPer.role_id')
		// 				->leftJoin('user as usr','usr.id','=','rolU.user_id')
		// 				->whereRaw('usr.id = "'.$userId.'" and permission.type_id = (select id from permission_type where permission_type.code = "menu" and permission.parent_id is null)')
		// 				->orderBy('permission.order')
		// 				->with(['children'=>function($query) use($userId){
		// 					$query->select('permission.*')
		// 					->leftJoin('permission_role as rPer','rPer.permission_id','=','permission.id')
		// 					->leftJoin('user_role as rolU','rolU.role_id','=','rPer.role_id')
		// 					->leftJoin('user as usr','usr.id','=','rolU.user_id')
		// 					->whereRaw('usr.id = "'.$userId.'" and permission.type_id = (select id from permission_type where permission_type.code = "menu")')
		// 					->orderBy('permission.order')
		// 					->get();
		// 				}])
		// 				->get();
		// 			}])
		// 			->leftJoin('permission as parent','parent.module_id','=','module.id')
		// 			->leftJoin('permission as child','child.parent_id','=','parent.id')
		// 			->whereRaw("module.state=1 and module.id in (SELECT per.module_id FROM permission per left JOIN permission_role rPer ON rPer.permission_id = per.id left join user_role rolU on rolU.role_id = rPer.role_id left join user on `user`.id = rolU.user_id where user.id = ".$userId.") and parent.type_id = (select id from permission_type where code = 'menu')")
		// 			->groupBy('module.name')
		// 			->orderBy('module.order')
		// 			->orderBy('parent.order')
		// 			->get();

		$query =	Permission::selectRaw('distinct permission.*')
					->join('module as module','permission.module_id','=','module.id')
					->Join('permission_role as rPer','rPer.permission_id','=','permission.id')
					->join('user_role as rolU','rolU.role_id','=','rPer.role_id')
					->join('user as usr','usr.id','=','rolU.user_id')
					->with(['children'=>function($query) use($userId){
							$query->select('permission.*')
							->join('permission as parent','parent.id','=','permission.parent_id')
							->join('permission_role as rPer','rPer.permission_id','=','permission.id')
							->join('user_role as rolU','rolU.role_id','=','rPer.role_id')
							->join('user as usr','usr.id','=','rolU.user_id')
							->whereRaw('usr.id = "'.$userId.'" and permission.type_id = (select id from permission_type where permission_type.code = "menu")')
							->orderBy('permission.order asc')
							->get();
					}])
					->whereRaw("module.state=1 and module.id in (select module.id from module where module.state = 1) and permission.type_id = (select id from permission_type where code = 'menu') and usr.id = ".$userId." AND permission.parent_id IS NULL")
					->groupBy('module.name')
					->orderBy('permission.order')
					->get();

		return $query;
	}


	public function loadAdminMenu()
	{
		$query = Permission::select('permission.*')->with(['children' => function($query) {
			$query->select('permission.*')
			->join('module','module.id','=','permission.module_id')
			->join('permission as parent','parent.id','=','permission.parent_id')
			->whereRaw("permission.type_id = (SELECT permission_type.id FROM permission_type WHERE permission_type.`code` = 'menu') order by module.order, permission.`order` asc")->get();
		}])->join('module','module.id','=','permission.module_id')->whereRaw("permission.type_id = (SELECT permission_type.id FROM permission_type WHERE permission_type.`code` = 'menu') AND permission.parent_id IS NULL ORDER BY module.order, permission.`order` asc")->get();
		
		return $query;
	}
}