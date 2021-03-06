<?php
namespace Futbol\Repository;

use Futbol\RepositoryInterface\StudentRepositoryInterface;
use Futbol\Events\DeleteEnrollmentGroup;
use Futbol\Exceptions\StudentException;
use Futbol\Models\EnrollmentGroup;
use Futbol\Models\Enrollment;
use Futbol\Models\PersonType;
use Futbol\Models\GroupClass;
use Futbol\Models\Student;
use Futbol\Models\Season;
use Futbol\Models\Person;
use Futbol\Models\Role;
use Futbol\Models\User;
use DB;

/**
* 
*/
class StudentRepository implements StudentRepositoryInterface
{
	
	public function paginate()
	{
		return Student::paginate();
	}

	public function enum($params = null)
	{
		$users = Student::all();

		if (!$users) {
			throw new StudentException('No se han encontrado el listado de  estudiantes',"404");
		}
		return $users;
	}



	public function find($field)
	{
		if(is_int($field) || is_string($field)) {
			$student = Student::where('id',$field)->first();
		} else {
			throw new StudentException('Se ha producido un error al buscar el estudiante',"500");	
		}

		if (!$student) throw new StudentException('No se puede buscar al estudiante',"404");	
		
		return $student;

	}

	//TODO
	public function save($data)
	{
		//save person representant
		$dataRepresentant = $data['representant'];
		
		$existPersonRepresentant = false;
		if ( (isset($dataRepresentant['user_id'])) && ($dataRepresentant['user_id'] != null) && ($dataRepresentant['person_id'] != null ) ) {
			$personRepresentant = Person::find($dataRepresentant['person_id']);
			$existPersonRepresentant = true;
		} else {
			$personRepresentant = new Person();
		}
		
		$dataRepresentant['person_type_id'] = $this->getPersonType();
		$personRepresentant->fill($dataRepresentant);
		$existPersonRepresentant ? $savedPersonRepresentant = $personRepresentant->update() : $savedPersonRepresentant = $personRepresentant->save();
		if(!$savedPersonRepresentant) {
			throw new StudentException('Ha ocurrido un error al guardar el estudiante '.$data['name'],"500");
		}

		//insert person on data for user represenant
		$dataRepresentant['person_id'] = $personRepresentant->getKey();
		if ($existPersonRepresentant) {
			$userRepresentant = User::find($dataRepresentant['user_id']);
		} else {
			$userRepresentant = new User();
			$dataRepresentant['password'] = (new User())->generateGenericPass();
			
		}
		//save user representant
		$userRepresentant->fill($dataRepresentant);
		$existPersonRepresentant ? $savedUserRepresentant =  $userRepresentant->update() : $savedUserRepresentant = $userRepresentant->save();
		if(!$savedUserRepresentant) {
			throw new StudentException('Ha ocurrido un error al guardar el estudiante '.$data['name'],"500");
		}


		if(!$existPersonRepresentant) {
			//Set Role Representante
			$roleId = Role::where('code','representante')->first()->id;
			$userRepresentant->roles()->attach($roleId);
		}
		
		
		//save student
		$person = new Person();
		$data['person_type_id'] = $this->getPersonType();
		$person->fill($data);
		if ($person->save()) {
			$personId = $person->getKey();
			$student = new Student();
			$data['person_id'] = $personId;
			$data['representant_id'] = $personRepresentant->getKey();
			$student->fill($data);
			if ($saved = $student->save()) {
				
				//save Inscription
				$dataEnrollment = $data['enrollment'];
				
				$dataEnrollment['student_id'] = $student->getKey();
				$dataEnrollment['state'] = Enrollment::ACTIVE;
				$enrollment = new Enrollment();
				$enrollment->fill($dataEnrollment);

				if($saveEnrollment =  $enrollment->save() ) {
					//save groups
					if ($student->state == Student::ACTIVE) {
						foreach ($dataEnrollment['groups'] as $key => $gr) {
							$enrGroup = new EnrollmentGroup(['group_id' => $gr]);
							$enrollment->groups()->save($enrGroup);
						}
					
						$enrollment->insertCapacitiesGroups($dataEnrollment['groups']);
					}

					return  $this->find($student->getKey());
				}
				
			} else {
				
				throw new StudentException('Ha ocurrido un error al guardar el estudiante '.$data['name'],"500");
			}
		} else {
			
			throw new StudentException('Ha ocurrido un error al guardar el estudiante '.$data['name'],"500");
		}
		
	}

	public function edit($id,$data)
	{
		//save person representant
		$dataRepresentant = $data['representant'];
		
		$existPersonRepresentant = false;
		if (($dataRepresentant['user_id'] != null) && ($dataRepresentant['person_id'] != null ) ) {
			$personRepresentant = Person::find($dataRepresentant['person_id']);
			$existPersonRepresentant = true;
		} else {
			$personRepresentant = new Person();
		}

		$dataRepresentant['person_type_id'] = $this->getPersonType();
		$personRepresentant->fill($dataRepresentant);
		$existPersonRepresentant ? $savedPersonRepresentant = $personRepresentant->update() : $savedPersonRepresentant = $personRepresentant->save();
		if(!$savedPersonRepresentant) {
			throw new StudentException('Ha ocurrido un error al actualizar el estudiante '.$data['name'],"500");
		}

		//insert person on data for user represenant
		// $dataRepresentant['person_id'] = $personRepresentant->getKey();
		if ($existPersonRepresentant) {
			$userRepresentant = User::find($dataRepresentant['user_id']);
		} else {
			$userRepresentant = new User();
			$dataRepresentant['password'] = (new User())->generateGenericPass();
			
		}
		//save user representant
		$userRepresentant->fill($dataRepresentant);
		$existPersonRepresentant ? $savedUserRepresentant =  $userRepresentant->update() : $savedUserRepresentant = $userRepresentant->save();
		
		if(!$savedUserRepresentant) {
			throw new StudentException('Ha ocurrido un error al actualizar el estudiante '.$data['name'],"500");
		}

		if(!$existPersonRepresentant) {
			//Set Role Representante
			$roleId = Role::where('code','representante')->first()->id;
			$userRepresentant->roles()->attach($roleId);
		}



		//save student
		$student = Student::find($id);
		if(!$student) {
			throw new StudentException('Ha ocurrido un error al actualizar el estudiante '.$data['name'],"500");
		}
		
		$data['person_type_id'] = $this->getPersonType();
		$student->person->fill($data);
		if($savePerson = $student->person->update()) {
			//update student
			$personId = $student->person->getKey();
			$data['person_id'] = $personId;
			$data['representant_id'] = $personRepresentant->getKey();
			$student->fill($data);
			if ($saveStudent = $student->update()) {
				
				//save Inscription
				$dataEnrollment = $data['enrollment'];
				$dataEnrollment['student_id'] = $student->getKey();
				$enrollment = $student->currentEnrollment();
				//$dataEnrollment['state'] = Enrollment::ACTIVE;
				
				//if change a group
				$updateGroupClass = false;

				if(array_key_exists('is_changing_group',$data) && $data['is_changing_group'] == '1' && $data['state'] == 1) {
					$newGroups = $data['enrollment']['groups'];
					$oldGroups = [];
					foreach($enrollment->groups as $oldGrObj) {
						$oldGroups[] = $oldGrObj->group_id;
					}
					
					event(new DeleteEnrollmentGroup($newGroups, $oldGroups, $enrollment->id));
					$updateGroupClass = true;

				} elseif ($data['state'] == 0) {
					
					$enrollment = $student->currentEnrollment();
					$newGroups = [];
					$oldGroups = [];
					foreach($enrollment->groups as $oldGrObj) {
						$oldGroups[] = $oldGrObj->group_id;
					}
					
					
					event(new DeleteEnrollmentGroup($newGroups, $oldGroups, $enrollment->id));
					$updateGroupClass = true;
				}
				$enrollment->fill($dataEnrollment);

				if($saveEnrollment =  $enrollment->save() ) {
					if($updateGroupClass) {
						// foreach ($newGroups as $key => $newGr) {
						// 	$enrGroup = new EnrollmentGroup(['group_id' => $newGr]);
						// 	$enrollment->groups()->save($enrGroup);
						// }
						$enrollment->updateCapacitiesGroups($oldGroups,$newGroups);
					}
					return  $this->find($student->getKey());
				}
					
				
			} else {
				
				throw new StudentException('Ha ocurrido un error al actualizar el estudiante '.$data['name'],"500");
			}
		}


	}

	public function remove($id)
	{
		if ($student = $this->find($id)) {
			foreach ($student->enrollments as $key => $enrollment) {
				foreach ($enrollment->groups as $key => $group) {
					$group->assistances()->delete();
				}
				$enrollment->delete();
			}
			$student->delete();
			return true;
		}
		throw new StudentException('Ha ocurrido un error al eliminar el estudiante ',"500");
	}

	public function getModel()
	{
		return new Student();
	}


	public function getPersonType($code = null) {
        return PersonType::select('id')->where('code', $code ? $code : 'persona-natural' )->first()->id;
    }


    /**
     * extrae el total de estudiantes activos
     *  en la temporada actual
     */
    public function getTotalStudents()
    {
    	$sql =  "SELECT COUNT(*) total_students
    	 			FROM student
					WHERE id IN 
						(
							SELECT enrollment.student_id
							FROM enrollment
							WHERE enrollment.deleted_at IS NULL AND enrollment.state = 1 AND enrollment.season_id 
							IN (
								SELECT id
								FROM season
								WHERE season.state = 1 AND season.deleted_at IS NULL
							)
						) 
					AND student.deleted_at IS NULL ;";

		$total = DB::select(DB::raw($sql));
		
		return $total;

    }


    public function insertFromRegisterForm($data)
    {
    	
    	//Roles from table 
    	$repreRole = Role::select('id')->where('code','representant')->orWhere('code','representante')->first();

    	if (!$repreRole) {
    		throw new StudentException("No se encontró el rol de representante", 404);
    	}

    	$dataRepresentant = $data['representant'];
    	$dataRepresentant['person_type_id'] = $this->getPersonType();

    	$userRepresentant = User::where('email',$dataRepresentant['email'])->first();

    	//si existe usuario representante con ese correo
    	if ($userRepresentant) {

    		//si el usuario tiene rol de representante
    		$hasRoleRep = $userRepresentant->hasRole($repreRole->id);
    		if (!$hasRoleRep) {
    			//si no tiene el rol de representante
    			$userRepresentant->roles()->attach($repreRole->id);
    		}
			$personRepresentant = $userRepresentant->person;
			$personRepresentant->fill($dataRepresentant)->update();
			$userRepresentant->fill($dataRepresentant)->update();
    	} else {
    		
    		$personRepresentant = new Person();
    		$personRepresentant->fill($dataRepresentant);
    		$savedPersonRepresentant = $personRepresentant->save();
    		
    		if(!$savedPersonRepresentant) {
				throw new StudentException('Ha ocurrido un error al guardar el representante '.$data['name'],"500");
			}

    		$idPerson = $personRepresentant->getKey();
    		$userRepresentant = new User();
    		$dataRepresentant['person_id'] = $idPerson;
			$dataRepresentant['password'] = (new User())->generateGenericPass();
			//save user representant
			$userRepresentant->fill($dataRepresentant);

			$savedUserRepresentant = $userRepresentant->save();

			if(!$savedUserRepresentant) {
				throw new StudentException('Ha ocurrido un error al guardar el usuario representante '.$data['name'],"500");
			}

			$userRepresentant->roles()->attach($repreRole->id);

			$userRepresentant = $userRepresentant;

    	}


    	if($personRepresentant->hasStudents()) {

    		$students = $personRepresentant->getStudents();
			
    		foreach ($students as $key => $student) {
    			if ($student->person->name == $data['name'] && $student->person->last_name == $data['last_name']) {
    				throw new StudentException("Ya existe una solicitud ingresada", 500);
    			}
			}
			
			$personStudent = new Person();
			$data['person_type_id'] = $this->getPersonType();
			$personStudent->fill($data);

			if ($personStudent->save()) {
				$personId = $personStudent->getKey();
				$student = new Student();
				$data['person_id'] = $personId;
				$data['representant_id'] = $personRepresentant->getKey();
				$data['state'] = Student::ACTIVE;
				$student->fill($data);
				if ($saved = $student->save()) {

					$dataEnrollment = $data['enrollment'];
					$dataEnrollment['season_id'] = (new Season())->getSeasonActive()->id;
					$dataEnrollment['student_id'] = $student->getKey();
					$dataEnrollment['state'] = Enrollment::ACTIVE;
					$dataEnrollment['class_type'] = Enrollment::FREE;
					$dataEnrollment['day'] = get_day_from_key($dataEnrollment['day']);
					$dataEnrollment['field_id'] = (new FieldRepository())->find(['name'=>$dataEnrollment['field_id']])->id;
					$enrollment = new Enrollment();
					$enrollment->fill($dataEnrollment);

					if ($saveEnrollment = $enrollment->save()) {
						$dataEnrollment['enrollment_id'] = $enrollment->getKey();
						$dataEnrollment['age'] = $student->person->age;
						$groupIdToInsert = (new GroupClass())->getAvailableGroupByParams($dataEnrollment);
						
						if (!$groupIdToInsert) {
							throw new StudentException('Ha ocurrido un error el grupo para el estudiante '.$data['name'],"500");
						}

						$enrGroup = new EnrollmentGroup(['group_id' => $groupIdToInsert]);
						$enrollment->groups()->save($enrGroup);

						$dataEnrollment['groups'] = [];
						$dataEnrollment['groups'][] = $groupIdToInsert;
						$enrollment->insertCapacitiesGroups($dataEnrollment['groups']);

						
					}
				}
			}

    	} else {

    		$personStudent = new Person();
			$data['person_type_id'] = $this->getPersonType();
			$personStudent->fill($data);

			if ($personStudent->save()) {
				$personId = $personStudent->getKey();
				$student = new Student();
				$data['person_id'] = $personId;
				$data['representant_id'] = $personRepresentant->getKey();
				$data['state'] = Student::ACTIVE;
				$student->fill($data);
				if ($saved = $student->save()) {

					$dataEnrollment = $data['enrollment'];
					$dataEnrollment['season_id'] = (new Season())->getSeasonActive()->id;
					$dataEnrollment['student_id'] = $student->getKey();
					$dataEnrollment['state'] = Enrollment::ACTIVE;
					$dataEnrollment['class_type'] = Enrollment::FREE;
					$dataEnrollment['day'] = get_day_from_key($dataEnrollment['day']);
					$dataEnrollment['field_id'] = (new FieldRepository())->find(['name'=>$dataEnrollment['field_id']])->id;
					$enrollment = new Enrollment();
					$enrollment->fill($dataEnrollment);

					if ($saveEnrollment = $enrollment->save()) {
						$dataEnrollment['enrollment_id'] = $enrollment->getKey();
						$dataEnrollment['age'] = $student->person->age;
						$groupIdToInsert = (new GroupClass())->getAvailableGroupByParams($dataEnrollment);
						
						if (!$groupIdToInsert) {
							throw new StudentException('Ha ocurrido un error el grupo para el estudiante '.$data['name'],"500");
						}

						$enrGroup = new EnrollmentGroup(['group_id' => $groupIdToInsert]);
						$enrollment->groups()->save($enrGroup);

						$dataEnrollment['groups'] = [];
						$dataEnrollment['groups'][] = $groupIdToInsert;
						$enrollment->insertCapacitiesGroups($dataEnrollment['groups']);

						
					}
				}
			}


    	}



    	return $userRepresentant;
    }
}