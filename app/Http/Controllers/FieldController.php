<?php

namespace App\Http\Controllers;
use App\Models\Field;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\AvailableDate;

class FieldController extends Controller
{
    public function index()
    {
        $fields = Field::all();
        $totalFields = Field::count();
        return view('admin.manageFields', compact('fields', 'totalFields'));
    }

    public function create()
    {
        return view('admin.createField');
    }

    public function store(Request $request)
    {
        $request->validate([
            'field_name' => 'required|string|max:255',
            'location' => 'required|string',
            'availability' => 'required|string',
            'price' => 'required|numeric',
            'owner_id' => 'nullable|exists:users,id',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        Field::create($request->all());
        return redirect()->route('admin.manageFields')->with('success', 'Field added successfully!');
    }

    public function edit($id)
    {
        $field = Field::findOrFail($id);
        return view('admin.editField', compact('field'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'field_name' => 'required|string|max:255',
            'location' => 'required|string',
            'availability' => 'required|string',
            'price' => 'required|numeric',
            'owner_id' => 'nullable|exists:users,id',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        $field = Field::findOrFail($id);
        $field->update($request->all());
        return redirect()->route('admin.manageFields')->with('success', 'Field updated successfully!');
    }

    public function destroy($id)
    {
        Field::destroy($id);
        return redirect()->route('admin.manageFields')->with('success', 'Field deleted successfully!');
    }


//    public function showFieldDetails($id)
//    {
//        // جلب تفاصيل الملعب من قاعدة البيانات
//        $field = Field::findOrFail($id);
////dd($field);
//        // تمرير البيانات إلى الـ View
//        return view('theme.FieldDetails', compact('field'));
//    }


//    public function showFieldDetails($fieldId)
//    {
//        // جلب بيانات الملعب
//        $field = Field::findOrFail($fieldId);
//
//        // جلب المواعيد المحجوزة لهذا الملعب
//        $bookedDates = Booking::where('field_id', $fieldId)->pluck('date_time')->toArray();
//
//
//        // جلب جميع المواعيد المتاحة مع استبعاد المحجوزة
//        $dates = AvailableDate::whereNotIn('date_time', $bookedDates)
//            ->where('field_id', $fieldId)
//            ->get();
//
//
//        return view('theme.FieldDetails', compact('field', 'dates'));
//    }

//    public function showFieldDetails(Request $request, $fieldId)
//    {
//        // جلب بيانات الملعب
//        $field = Field::findOrFail($fieldId);
//
//        // إضافة السجل الجديد إذا تم إرسال البيانات من النموذج
//        if ($request->has(['start_date_time', 'end_date_time'])) {
//            $request->validate([
//                'start_date_time' => 'required|date',
//                'end_date_time' => 'required|date|after_or_equal:start_date_time',
//            ]);
//
//            AvailableDate::create([
//                'field_id' => $fieldId,
//                'start_date_time' => $request->input('start_date_time'),
//                'end_date_time' => $request->input('end_date_time'),
//            ]);
//        }
//
//        // جلب المواعيد المحجوزة
//        $bookedDates = Booking::where('field_id', $fieldId)->pluck('date_time')->toArray();
//
//        // جلب جميع المواعيد المتاحة مع استبعاد المحجوزة
//        $dates = AvailableDate::whereNotIn('date_time', $bookedDates)
//            ->where('field_id', $fieldId)
//            ->get();
//
//        return view('theme.FieldDetails', compact('field', 'dates'));
//    }


    public function showFieldDetails(Request $request, $fieldId)
    {
        // Retrieve field details
        $field = Field::findOrFail($fieldId);

        // Add the new availability date if sent from the form
        if ($request->has(['start_date_time', 'end_date_time'])) {
            $request->validate([
                'date_time' => 'required|date',
                'start_date_time' => 'required|date',
                'end_date_time' => 'required|date|after_or_equal:start_date_time',
            ]);

            AvailableDate::create([
                'field_id' => $fieldId,
                'date_time' => $request->input('date_time'),
                'start_date_time' => $request->input('start_date_time'),
                'end_date_time' => $request->input('end_date_time'),
            ]);
        }

        // Retrieve booked dates for the given field
        $bookedDates = Booking::where('field_id', $fieldId)->pluck('date_time')->toArray();

        // Retrieve available dates excluding booked ones
        $dates = AvailableDate::whereNotIn('date_time', $bookedDates)
            ->where('field_id', $fieldId)
            ->get();

        return view('theme.FieldDetails', compact('field', 'dates'));
    }

}
