<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW income_union_view AS
            SELECT
                p.id,
                p.coupon_id,
                NULL AS category_id,
                CONCAT('Payment #', p.id) AS title,
                'Payment' AS source,
                p.amount_confirmed AS amount,
                'auto' AS type,
                CASE
                    WHEN p.coupon_id = 7 THEN 'books'
                    WHEN p.coupon_id = 8 THEN 'course (installment)'
                    ELSE 'course'
                END AS category,
                GROUP_CONCAT(
                    DISTINCT
                    COALESCE(c.title, pp.title, pp.from)
                    SEPARATOR ', '
                ) AS details,
                p.paid_at,
                p.created_at
            FROM
                payments p
                JOIN payment_items pi ON pi.payment_id = p.id
                LEFT JOIN courses c ON pi.course_id = c.id
                LEFT JOIN package_plans pp ON pi.package_plan_id = pp.id
            WHERE
                p.status = 'paid'
            GROUP BY
                p.id,
                p.coupon_id,
                p.amount_confirmed,
                p.paid_at,
                p.created_at

            UNION ALL

            SELECT
                ae.id,
                NULL AS coupon_id,
                ac.id AS category_id,
                ae.title AS title,
                'Accounting Entry' AS source,
                ae.amount AS amount,
                'manual' AS type,
                ac.name AS category,
                CONCAT_WS(', ', ae.description, ac.description) AS details,
                ae.transaction_date AS paid_at,
                ae.created_at
            FROM
                accounting_entries ae
                JOIN accounting_categories ac ON ae.category_id = ac.id
            WHERE
                ac.type = 'income';
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS income_union_view');
    }
};
