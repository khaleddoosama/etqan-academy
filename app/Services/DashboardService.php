<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{

    public function getEventFrequencyOverTime()
    {
        return Cache::remember('eventFrequencyOverTime', 60 * 60 * 3, function () {
            // Fetch events for the last 10 days
            $last10Days = DB::select("
            SELECT DATE(created_at) AS date, COUNT(*) AS event_count
            FROM activity_log
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 10 DAY)
            GROUP BY date
            ORDER BY date ASC
        ");

            // Fetch events for the same days last month
            $sameDaysLastMonth = DB::select("
            SELECT DATE(created_at) AS date, COUNT(*) AS event_count
            FROM activity_log
            WHERE created_at >= DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 30 DAY), INTERVAL 10 DAY)
              AND created_at < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY date
            ORDER BY date ASC
        ");

            // Calculate total events for each period
            $totalLast10Days = array_sum(array_map(fn($item) => $item->event_count, $last10Days));
            $totalSameDaysLastMonth = array_sum(array_map(fn($item) => $item->event_count, $sameDaysLastMonth));

            // Calculate percentage change
            if ($totalSameDaysLastMonth == 0) {
                $percentageChange = $totalLast10Days > 0 ? 100 : 0;
            } else {
                $percentageChange = (($totalLast10Days - $totalSameDaysLastMonth) / $totalSameDaysLastMonth) * 100;
            }

            return [
                'last_10_days' => $last10Days,
                'same_days_last_month' => $sameDaysLastMonth,
                'percentage_change' => round($percentageChange, 2), // Round to two decimal places
            ];
        });
    }

    public function getCountEventsFrequencyOverTime()
    {
        return Cache::remember('countEventsFrequencyOverTime', 60 * 60 * 3, function () {
            $result = DB::select("
                SELECT COUNT(*) AS event_count
                FROM activity_log
            ");

            return $result[0]->event_count ?? 0;
        });
    }

    public function getMostAccessedURLs()
    {
        return Cache::remember('mostAccessedURLs', 60 * 60, function () {
            return DB::select(
                "
                SELECT JSON_UNQUOTE(JSON_EXTRACT(properties, '$.url')) AS url, COUNT(*) AS access_count
                FROM activity_log WHERE JSON_UNQUOTE(JSON_EXTRACT(properties, '$.url')) IS NOT NULL
                GROUP BY url
                ORDER BY access_count DESC
                LIMIT 10
                "
            );
        });
    }

    public function getMostAccessedCourses()
    {
        return Cache::remember('mostAccessedCourses', 60 * 60 * 6, function () {
            return DB::select("SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(properties, '$.url')), '/course/', -1), '/', 1) AS course, COUNT(*) AS access_count FROM activity_log WHERE JSON_UNQUOTE(JSON_EXTRACT(properties, '$.url')) LIKE '%/course/%' GROUP BY course ORDER BY access_count DESC LIMIT 10");
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

        return Cache::remember('uniqueIPs_' . ($limit ?? 'all'), 60 * 60 * 12, function () use ($query) {
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



    public function getPeakHours()
    {
        return Cache::remember('peakHours', 60 * 60, function () {
            return DB::select("SELECT HOUR(created_at) AS hour, COUNT(*) AS activity_count FROM activity_log GROUP BY hour ORDER BY activity_count DESC");
        });
    }

    public function getIpCounts()
    {
        return Cache::remember('ipCounts', 60 * 60 * 6, function () {
            return DB::select("SELECT JSON_UNQUOTE(JSON_EXTRACT(properties, '$.ip')) AS ip_address, COUNT(*) AS access_count FROM activity_log GROUP BY ip_address ORDER BY access_count DESC");
        });
    }

    public function getStudentsPerCourse()
    {
        return Cache::remember('studentsPerCourse', 60 * 60 * 6, function () {
            return DB::select("SELECT c.title as course_title, COUNT(uc.student_id) as student_count FROM user_courses uc INNER JOIN courses c ON c.id = uc.course_id GROUP BY uc.course_id, c.title ORDER BY student_count DESC");
        });
    }

    public function getTopStudentsCourses()
    {
        return Cache::remember('topStudentsCourses', 60 * 60 * 6, function () {
            return DB::select("SELECT u.first_name, u.last_name, COUNT(uc.course_id) as course_count FROM user_courses uc JOIN users u ON uc.student_id = u.id GROUP BY uc.student_id, u.first_name, u.last_name ORDER BY course_count DESC LIMIT 10");
        });
    }

    public function getCityActivity()
    {
        return Cache::remember('cityActivity', 60 * 60 * 6, function () {
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
        // return Cache::remember('activityByDayOfWeek', 60 * 60 * 12, function () {
        $today = Carbon::now();

        // تحديد بداية الأسبوع (السبت)
        $startOfWeek = $today->copy()->startOfWeek(Carbon::SATURDAY);

        // البيانات لهذا الأسبوع (تبدأ من السبت وتزداد كل يوم)
        $thisWeek = DB::select(
            "
                        SELECT d.day_of_week, COALESCE(a.activity_count, 0) AS activity_count
                        FROM (
                            SELECT 1 AS day_of_week UNION ALL
                            SELECT 2 UNION ALL
                            SELECT 3 UNION ALL
                            SELECT 4 UNION ALL
                            SELECT 5 UNION ALL
                            SELECT 6 UNION ALL
                            SELECT 7
                        ) d
                        LEFT JOIN (
                            SELECT DAYOFWEEK(created_at) AS day_of_week, COUNT(*) AS activity_count
                            FROM activity_log
                            WHERE DATE(created_at) >= DATE(:start_of_week) AND DATE(created_at) <= DATE(:today)
                            GROUP BY day_of_week
                        ) a ON d.day_of_week = a.day_of_week
                        ORDER BY d.day_of_week
                    ",
            [
                'start_of_week' => $startOfWeek,
                'today' => $today,
            ]
        );


        // البيانات للأسبوع السابق بالكامل
        $lastWeekStart = $startOfWeek->copy()->subWeek();
        $lastWeekEnd = $startOfWeek->copy()->subDay();

        $lastWeek = DB::select(
            "
                            SELECT DAYOFWEEK(created_at) AS day_of_week, COUNT(*) AS activity_count
                            FROM activity_log
                            WHERE DATE(created_at) >= DATE(:last_week_start) AND DATE(created_at) <= DATE(:last_week_end)
                            GROUP BY day_of_week
                            ORDER BY day_of_week
                        ",
            [
                'last_week_start' => $lastWeekStart,
                'last_week_end' => $lastWeekEnd,
            ]
        );
        // dd($thisWeek, $lastWeek);
        // حساب إجمالي الأحداث لكل فترة
        $totalThisWeek = 0;
        $totalLastWeek = 0;
        for ($i = 0; $i < 7; $i++) {
            $totalThisWeek += $thisWeek[$i]->activity_count ?? 0;
            if ($thisWeek[$i]->activity_count != 0) {
                $totalLastWeek += $lastWeek[$i]->activity_count ?? 0;
            }
        }

        // حساب التغيير بالنسبة المئوية
        if ($totalLastWeek == 0) {
            $percentageChange = $totalThisWeek > 0 ? 100 : 0;
        } else {
            $percentageChange = (($totalThisWeek - $totalLastWeek) / $totalLastWeek) * 100;
        }

        // إرجاع البيانات
        return [
            'this_week' => $thisWeek,
            'last_week' => $lastWeek,
            'percentage_change' => $percentageChange,
        ];
        // });
    }

    public function getActiveUserCount()
    {
        return Cache::remember('activeUserCount', 60 * 60, function () {
            return DB::table('users')->where('status', '1')->count();
        });
    }

    public function getActiveLectureCount()
    {
        return Cache::remember('activeLectureCount', 60 * 60, function () {
            return DB::table('lectures')->where('processed', '1')->count();
        });
    }

    public function getActiveCourseCount()
    {
        return Cache::remember('activeCourseCount', 60 * 60, function () {
            return DB::table('courses')->where('status', '1')->count();
        });
    }

    public function getBrowsers()
    {
        return Cache::remember('browsers', 60 * 60, function () {
            return DB::select("SELECT
                CASE
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%Edg%' THEN 'Edge'
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%Chrome%' AND JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) NOT LIKE '%Edg%' THEN 'Chrome'
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%Safari%' AND JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) NOT LIKE '%Chrome%' THEN 'Safari'
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%Firefox%' THEN 'Firefox'
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%SamsungBrowser%' THEN 'Samsung Browser'
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%Opera%' OR JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%OPR%' THEN 'Opera'
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%Trident%' OR JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%MSIE%' THEN 'Internet Explorer'
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%YaBrowser%' THEN 'Yandex Browser'
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%Vivaldi%' THEN 'Vivaldi'
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%Brave%' THEN 'Brave'
                WHEN JSON_UNQUOTE(JSON_EXTRACT(properties, '$.user_agent')) LIKE '%DuckDuckGo%' THEN 'DuckDuckGo'
                ELSE 'Other/Unknown'
                END AS browser,
                COUNT(*) AS count
            FROM activity_log
            GROUP BY browser
            ORDER BY count DESC;
            ");
        });
    }

    public function getLastMembers()
    {
        return Cache::remember('lastMembers', 60 * 5, function () {
            return User::orderBy('created_at', 'desc')->select('first_name', 'last_name', 'created_at')->limit(8)->get();
        });
    }
}
