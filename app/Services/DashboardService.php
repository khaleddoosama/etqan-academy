<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public $cacheDuration;
    //constructor
    public function __construct()
    {
        $this->cacheDuration = 60 * 60 * 24;
    }

    public function getEventFrequencyOverTime()
    {
        return Cache::remember('eventFrequencyOverTime', $this->cacheDuration, function () {
            return DB::select("SELECT DATE(created_at) AS date, COUNT(*) AS event_count FROM activity_log GROUP BY date ORDER BY date ASC");
        });
    }

    public function getMostAccessedURLs()
    {
        return Cache::remember('mostAccessedURLs', $this->cacheDuration, function () {
            return DB::select("SELECT JSON_UNQUOTE(JSON_EXTRACT(properties, '$.url')) AS url, COUNT(*) AS access_count FROM activity_log WHERE JSON_UNQUOTE(JSON_EXTRACT(properties, '$.url')) IS NOT NULL GROUP BY url ORDER BY access_count DESC LIMIT 10");
        });
    }

    private function getUniqueIPs($limit = null)
    {
        $query = "SELECT JSON_UNQUOTE(JSON_EXTRACT(properties, '$.ip')) AS ip_address, COUNT(*) AS access_count
              FROM activity_log
              WHERE JSON_UNQUOTE(JSON_EXTRACT(properties, '$.ip')) IS NOT NULL
              GROUP BY ip_address
              ORDER BY access_count DESC";

        if ($limit !== null) {
            $query .= " LIMIT $limit";
        }

        return Cache::remember('uniqueIPs', $this->cacheDuration, function () use ($query) {
            return DB::select($query);
        });
    }

    public function getUniqueIPCount()
    {
        return $this->getUniqueIPs();
    }

    public function getUniqueIPCounts()
    {
        return $this->getUniqueIPs(10);
    }

    public function getMostAccessedCourses()
    {
        return Cache::remember('mostAccessedCourses', $this->cacheDuration, function () {
            return DB::select("SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(properties, '$.url')), '/course/', -1), '/', 1) AS course, COUNT(*) AS access_count FROM activity_log WHERE JSON_UNQUOTE(JSON_EXTRACT(properties, '$.url')) LIKE '%/course/%' GROUP BY course ORDER BY access_count DESC LIMIT 10");
        });
    }

    public function getPeakHours()
    {
        return Cache::remember('peakHours', $this->cacheDuration, function () {
            return DB::select("SELECT HOUR(created_at) AS hour, COUNT(*) AS activity_count FROM activity_log GROUP BY hour ORDER BY activity_count DESC");
        });
    }

    public function getIpCounts()
    {
        return Cache::remember('ipCounts', $this->cacheDuration, function () {
            return DB::select("SELECT JSON_UNQUOTE(JSON_EXTRACT(properties, '$.ip')) AS ip_address, COUNT(*) AS access_count FROM activity_log GROUP BY ip_address ORDER BY access_count DESC");
        });
    }

    public function getStudentsPerCourse()
    {
        return Cache::remember('studentsPerCourse', $this->cacheDuration, function () {
            return DB::select("SELECT c.title as course_title, COUNT(uc.student_id) as student_count FROM user_courses uc INNER JOIN courses c ON c.id = uc.course_id GROUP BY uc.course_id, c.title ORDER BY student_count DESC");
        });
    }

    public function getTopStudentsCourses()
    {
        return Cache::remember('topStudentsCourses', $this->cacheDuration, function () {
            return DB::select("SELECT u.first_name, u.last_name, COUNT(uc.course_id) as course_count FROM user_courses uc JOIN users u ON uc.student_id = u.id GROUP BY uc.student_id, u.first_name, u.last_name ORDER BY course_count DESC LIMIT 10");
        });
    }

    public function getCityActivity()
    {
        return Cache::remember('cityActivity', $this->cacheDuration, function () {
            return DB::select("SELECT
                COUNT(*) AS Activity_Count,
                COALESCE(
                    JSON_UNQUOTE(JSON_EXTRACT(properties, '$.geo_location.city')),
                    JSON_UNQUOTE(JSON_EXTRACT(properties, '$.geo_location.cityName'))
                ) AS City
            FROM
                activity_log
            WHERE
                JSON_EXTRACT(properties, '$.geo_location') IS NOT NULL
            GROUP BY
                City
            ORDER BY
                Activity_Count DESC
            LIMIT 10
            ");
        });
    }

    public function getActivityByDayOfWeek()
    {
        return Cache::remember('activityByDayOfWeek', $this->cacheDuration, function () {
            return DB::select("SELECT DAYOFWEEK(created_at) AS day_of_week, COUNT(*) AS activity_count FROM activity_log GROUP BY day_of_week ORDER BY day_of_week");
        });
    }

    public function getActiveUserCount()
    {
        return Cache::remember('activeUserCount', $this->cacheDuration, function () {
            return DB::table('users')->where('status', '1')->count();
        });
    }

    public function getActiveLectureCount()
    {
        return Cache::remember('activeLectureCount', $this->cacheDuration, function () {
            return DB::table('lectures')->where('processed', '1')->count();
        });
    }

    public function getActiveCourseCount()
    {
        return Cache::remember('activeCourseCount', $this->cacheDuration, function () {
            return DB::table('courses')->where('status', '1')->count();
        });
    }
}
