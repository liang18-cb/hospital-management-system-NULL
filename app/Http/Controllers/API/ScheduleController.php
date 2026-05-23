<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\API\ScheduleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Schedule::with('doctor.user');

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        $schedules = $query->paginate(10);

        $data = [
            'items' => ScheduleResource::collection($schedules),
            'pagination' => [
                'current_page' => $schedules->currentPage(),
                'last_page' => $schedules->lastPage(),
                'per_page' => $schedules->perPage(),
                'total' => $schedules->total(),
            ]
        ];

        return $this->sendResponse(
            $data,
            'Data jadwal berhasil diambil'
        );
    }

    public function store(StoreScheduleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $schedule = Schedule::create($validated);
            DB::commit();

            return $this->sendResponse(
                new ScheduleResource($schedule->load('doctor.user')),
                'Data jadwal berhasil ditambahkan',
                201
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(string|int $id): JsonResponse
    {
        $schedule = Schedule::with('doctor.user')->findOrFail($id);

        return $this->sendResponse(
            new ScheduleResource($schedule),
            'Detail jadwal berhasil ditemukan'
        );
    }

    public function update(UpdateScheduleRequest $request, string|int $id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $schedule->update($validated);
            DB::commit();

            return $this->sendResponse(
                new ScheduleResource($schedule->refresh()),
                'Data jadwal berhasil diperbarui'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(string|int $id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);

        DB::beginTransaction();
        try {
            $schedule->delete();
            DB::commit();

            return $this->sendResponse(
                null,
                'Data jadwal berhasil dihapus'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}