<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the employees.
     */
    public function index()
    {
        $employees = Employee::latest()->paginate(10);

        return Inertia::render('Employees/Index', [
            'employees' => $employees,
        ]);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        return Inertia::render('Employees/Create');
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'code_employee' => 'nullable|string|max:255',
            'major_employee' => 'boolean',
        ]);

        // Convertir el valor del checkbox
        $validated['major_employee'] = $request->has('major_employee') && $request->major_employee;

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Empleado creado exitosamente.');
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        return Inertia::render('Employees/Edit', [
            'employee' => $employee,
        ]);
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees,email,' . $employee->id,
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'code_employee' => 'nullable|string|max:255',
            'major_employee' => 'boolean',
        ]);

        // Convertir el valor del checkbox
        $validated['major_employee'] = $request->has('major_employee') && $request->major_employee;

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Empleado eliminado exitosamente.');
    }
}
