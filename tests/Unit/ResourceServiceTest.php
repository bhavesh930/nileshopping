<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\ResourceService;
use App\Models\Folder;
use App\Models\FormField;
use App\Models\Status;
use App\Models\Form;
use App\Models\Example;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ResourceServiceTest extends TestCase
{
    use DatabaseTruncation;
   // use WithFaker;

    public function helperCreateExample($name){
        $example = new Example();
        $example->name = $name;
        $example->description = 'Lorem ipsum dolor';
        $example->status_id = Status::first()->id;
        $example->save();
        return $example->id;
    }

    public function helperCreateForm($name = 'test'){
        $form = new Form();
        $form->name = $name;
        $form->table_name = 'example';
        $form->read = 1;
        $form->edit = 1;
        $form->add = 1;
        $form->delete = 1;
        $form->pagination = 5;
        $form->save();
        $this->helperCreateStatusRecord();
        $this->helperCreateStatusRecord();
        $this->helperCreateFormField($form->id, 'name');
        $this->helperCreateFormField($form->id, 'description');
        $this->helperCreateFormField($form->id, 'status_id', true);
        return $form->id;
    }

    public function helperCreateFormField($formId, $columnName = 'test', $relation = false){
        $field = new FormField();
        $field->name = $columnName;
        $field->column_name = $columnName;
        $field->type = 'text';
        $field->browse = 1;
        $field->read = 1;
        $field->edit = 1;
        $field->add = 1;
        if($relation){
            $field->relation_table = 'status';
            $field->relation_column = 'name';
        }
        $field->form_id = $formId;
        $field->save();
    }

    public function helperCreateStatusRecord(){
        $status = new Status();
        $status->name = 'Lorem ipsum dolor';
        $status->class = 'primary';
        $status->save();
    }

    public function testAddMedia()
    {
        $folder = new Folder();
        $folder->name = 'Resource';
        $folder->resource = 1;
        $folder->save();
        $resourceService = new ResourceService();
        $file = UploadedFile::fake()->image('file.jpg');
        $request = array('column_name' => $file);
        $result = $resourceService->addMedia($request, 'column_name');
        $this->assertNotEmpty($result);
        $this->assertDatabaseHas('media',[
            'id' => $result,
            'name' => 'file.jpg',
        ]);
        return $result;
    }

    public function testGetMediaUrl(){
        $mediaId = $this->testAddMedia();
        $resourceService = new ResourceService();
        $result = $resourceService->getMediaUrl( $mediaId );
        if(strpos($result, 'file.jpg') !== false){
            $result = true;
        }else{
            $result = false;
        }
        $this->assertSame(true, $result);
    }

    public function testGetFullIndexHeader(){
        $this->helperCreateFormField(1, 'test_1');
        $this->helperCreateFormField(1, 'test_2');
        $this->helperCreateFormField(1, 'test_3');
        $this->helperCreateFormField(5, 'test_4');
        $resourceService = new ResourceService();
        $temp = $resourceService->getFullIndexHeader( 1 );
        $result = array();
        foreach($temp as $t){
            array_push($result, $t->column_name);
        }
        $expected = array(
            'test_1', 'test_2', 'test_3'
        );
        $this->assertSame($expected, $result);
    }

    public function testGetRelations(){
        $this->helperCreateStatusRecord();
        $this->helperCreateStatusRecord();
        $this->helperCreateFormField(1, 'test_1');
        $this->helperCreateFormField(1, 'test_2', true);
        $columns = FormField::where('form_id', '=', 1)->where('browse', '=', '1')->get();
        $resourceService = new ResourceService();
        $temp = $resourceService->getRelations( $columns );
        $result = array();
        foreach($temp['relation_test_2'] as $t){
            array_push($result, $t->name);
        }
        $expected = array('Lorem ipsum dolor', 'Lorem ipsum dolor');
        $this->assertSame($expected, $result);
    }

    public function testGetIndexDatas(){
        $formId = $this->helperCreateForm('Form name');
        $exampleId1 = $this->helperCreateExample('Lorem');
        $exampleId2 = $this->helperCreateExample('ipsum');
        $statusId = Status::first()->id;
        $resourceService = new ResourceService();
        $result = $resourceService->getIndexDatas( $formId );
        // PHP 8.1+ native PDO returns int columns as int (was string under PHP 7).
        $expectedEnableButtons = array(
            'read' => 1,
            'edit' => 1,
            'add' => 1,
            'delete' => 1,
        );
        $expectedData = array(
            array(
                'id' => $exampleId1,
                'name' => 'Lorem',
                'description' => 'Lorem ipsum dolor',
                'status_id' => $statusId,
                'relation_status_id' => 'Lorem ipsum dolor'
            ),
            array(
                'id' => $exampleId2,
                'name' => 'ipsum',
                'description' => 'Lorem ipsum dolor',
                'status_id' => $statusId,
                'relation_status_id' => 'Lorem ipsum dolor'
            ),
        );
        $this->assertSame($expectedEnableButtons, $result['enableButtons']);
        $this->assertSame($expectedData, $result['data']);
    }

    public function testGetColumnsForAdd(){
        $this->helperCreateFormField(1, 'test_1');
        $this->helperCreateFormField(1, 'test_2');
        $this->helperCreateFormField(1, 'test_3');
        $this->helperCreateFormField(5, 'test_4');
        $resourceService = new ResourceService();
        $temp = $resourceService->getColumnsForAdd( 1 );
        $result = array();
        foreach($temp as $t){
            array_push($result, $t->column_name);
        }
        $expected = array(
            'test_1', 'test_2', 'test_3'
        );
        $this->assertSame($expected, $result);
    }

    public function testAdd(){
        $formId = $this->helperCreateForm('Form name');
        $resourceService = new ResourceService();
        $request = array(
            'name'          => 'aaa',
            'description'   => 'bbb',
            'status_id'     => '7'
        );
        $result = $resourceService->add( $formId, 'example', $request );
        $this->assertDatabaseHas('example',[
            'name' => 'aaa',
            'description' => 'bbb',
            'status_id' => '7',
        ]);
    }
    
    public function testUpdate(){
        $formId = $this->helperCreateForm('Form name');
        $exampleId = $this->helperCreateExample('test');
        $resourceService = new ResourceService();
        $request = array(
            'name'          => 'updated',
            'description'   => 'updated',
            'status_id'     => '7'
        );
        $resourceService->update('example', $formId, $exampleId, $request);
        $this->assertDatabaseHas('example',[
            'id' => "$exampleId",
            'name' => 'updated',
            'description' => 'updated',
            'status_id' => '7',
        ]);
    }

    public function testShow(){
        $formId = $this->helperCreateForm('Form name');
        $exampleId = $this->helperCreateExample('test');
        $resourceService = new ResourceService();
        $result = $resourceService->show($formId, 'example', $exampleId);
        $expected = array(
            array(
                'name'  => 'name',
                'value' => 'test',
                'type'  => 'default'
            ),
            array(
                'name'  => 'description',
                'value' => 'Lorem ipsum dolor',
                'type'  => 'default'
            ),
            array(
                'name'  => 'status_id',
                'value' => 'Lorem ipsum dolor',
                'type'  => 'default'
            )
        );
        $this->assertSame($expected, $result);
    }

    public function testGetColumnsForEdit(){
        $formId = $this->helperCreateForm('Form name');
        $exampleId = $this->helperCreateExample('test');
        $statusId = Status::first()->id;
        $resourceService = new ResourceService();
        $result = $resourceService->getColumnsForEdit('example', $formId, $exampleId);
        $expected = array(
            array(
                'type' => 'text',
                'name' => 'name',
                'column_name' => 'name',
                'value' => 'test',
            ),
            array(
                'type' => 'text',
                'name' => 'description',
                'column_name' => 'description',
                'value' => 'Lorem ipsum dolor',
            ),
            array(
                'type' => 'text',
                'name' => 'status_id',
                'column_name' => 'status_id',
                'value' => $statusId,
            )
        );
        $this->assertSame($expected, $result);
    }
}
