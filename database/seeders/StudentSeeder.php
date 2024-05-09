<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Program;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arte = Program::where('name','Arte')->first();

        $canto = Program::where('name','Canto')->first();

        $baile = Program::where('name','Baile')->first();

        $jorgePuerto = Student::create(['name' => 'Jorge Puerto']);
        $stefanXu = Student::create(['name' => 'Stefan Xu']);
        $gonzalezGarcia = Student::create(['name' => 'González Garcia']);
        $rodrigoLopez = Student::create(['name' => 'Rodríguez López']);
        $juanAlbornoz = Student::create(['name' => 'Juan Albornoz']);
        $ricadorPerez = Student::create(['name' => 'Ricardo Perez']);
        $aliciaCenteno = Student::create(['name' => 'Alicia Centeno']);
        $juanKu = Student::create(['name' => 'Juan Ku']);
        $mariaDelgado = Student::create(['name' => 'Maria Delgado']);
        $karenSarmiento = Student::create(['name' => 'Karen Sarmiento']);
        $juanRuiz = Student::create(['name' => 'Juan Ruiz']);

        $manualPuga = Student::create(['name' => 'Manual Puga']);
        $luisPech = Student::create(['name' => 'Luis Pech']);
        $alejandroCantun = Student::create(['name' => 'Alejandro Cantun']);
        $ricardoOfarri = Student::create(['name' => 'Ricardo Ofarri']);
        $manuelPech = Student::create(['name' => 'Manuel Pech']);
        $josueDominguez = Student::create(['name' => 'Josue Dominguez']);
        $manuelDelgado = Student::create(['name' => 'Manuel Delgado']);
        $ricardoRuiz = Student::create(['name' => 'Ricardo Ruiz']);

        $aliciaCeballos = Student::create(['name' => 'Alicia Ceballos']);
        $juanUku = Student::create(['name' => 'Juan Uku']);
        $marinaGutierres = Student::create(['name' => 'Marina Gutierres']);
        $danielPerez = Student::create(['name' => 'Daniel Perez']);
        $mariaGonzales = Student::create(['name' => 'Maria Gonzales']);
        $marisolHernandez = Student::create(['name' => 'Marisol Hernandez']);


        $arte->students()->sync(Student::where('id','<','12')->pluck('id')->toArray());
        $studentsToCanto = array_merge(Student::whereBetween('id',[12,19])->pluck('id')->toArray(),[$karenSarmiento->id]);
        $canto->students()->sync($studentsToCanto);
        $baile->students()->sync(Student::whereBetween('id',[20,25])->pluck('id')->toArray());
    }
}
