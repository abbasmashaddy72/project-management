<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Site;
use App\Contracts\SiteVigilanceSite;
use App\Repositories\SiteRepository;
use App\Contracts\SiteVigilanceExceptionLogGroup;
use App\Events\ExceptionLogGroupCreatedEvent;
use App\Events\ExceptionLogGroupUpdatedEvent;
use App\Http\Requests\StoreExceptionLogRequest;
use App\Repositories\ExceptionLogGroupRepository;

class ExceptionLogsController extends Controller
{
    public function __invoke(StoreExceptionLogRequest $request): JsonResponse
    {
        /** @var Site */
        $site = SiteRepository::query()
            ->where('api_token', $request->string('api_token'))
            ->first();

        abort_if(!$site, 403);

        /** @var SiteVigilanceExceptionLogGroup|null $group */
        $group = ExceptionLogGroupRepository::query()
            ->where('file', $request->input('file'))
            ->where('type', $request->input('type'))
            ->where('line', $request->input('line'))
            ->first();

        if (!$group) {
            $group = $this->createExceptionLogGroup($request, $site);
        } else {
            $this->updateExceptionLogGroup($request, $group);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    protected function createExceptionLogGroup(StoreExceptionLogRequest $request, SiteVigilanceSite $site)
    {
        $group = ExceptionLogGroupRepository::create([
            'message' => $request->input('message'),
            'type' => $request->input('type'),
            'file' => $request->input('file'),
            'line' => $request->input('line'),
            'first_seen' => $request->input('thrown_at'),
            'last_seen' => $request->input('thrown_at'),
            'site_id' => $site->id,
        ]);

        $this->createExceptionLog($request, $group);

        event(new ExceptionLogGroupCreatedEvent($group));

        return $group;
    }

    protected function updateExceptionLogGroup(StoreExceptionLogRequest $request, SiteVigilanceExceptionLogGroup $group)
    {
        $timeInMinutesBetweenUpdates = config('sitevigilance.exceptions.notify_time_between_group_updates_in_minutes');
        $timeDiffInMinutesFromLastException = now()->diffInMinutes($group->last_seen);

        $group->update([
            'message' => $request->input('message'),
            'last_seen' => $request->input('thrown_at'),
        ]);

        $this->createExceptionLog($request, $group);

        if ($timeDiffInMinutesFromLastException > $timeInMinutesBetweenUpdates) {
            event(new ExceptionLogGroupUpdatedEvent($group));
        }
    }

    protected function createExceptionLog(StoreExceptionLogRequest $request, SiteVigilanceExceptionLogGroup $group)
    {
        $group->exceptionLogs()->create(
            $request->safe()->except('api_token'),
        );
    }
}
