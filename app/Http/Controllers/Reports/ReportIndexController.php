<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

class ReportIndexController extends Controller
{
    public function index()
    {
        $userAuth = auth()->user()->auth;

        $groups = [
            [
                'title' => 'Operations Reports',
                'description' => 'Consignment status, gate-out register, manifest, declarations and House BL breakdown.',
                'permission' => 'OperationsReport',
                'icon' => 'ship',
                'route' => route('reports.operations.consignment-status'),
                'reports' => [
                    'Consignment Status Summary',
                    'Gate-Out Register',
                    'Awaiting Clearance',
                    'Cargo Manifest',
                    'Declaration Register',
                    'House BL Breakdown',
                ],
            ],
            [
                'title' => 'Client Reports',
                'description' => 'Client invoice summaries, ledger statements, outstanding balances and payment history.',
                'permission' => 'ClientReport',
                'icon' => 'users',
                'route' => '#',
                'reports' => [
                    'Client Invoice Summary',
                    'Client Ledger Statement',
                    'Outstanding Balances',
                    'Waybill Register',
                    'Client Payment History',
                ],
            ],
            [
                'title' => 'Disbursement Reports',
                'description' => 'Disbursement analysis, expenditure by account, revenue vs expenditure and waste sheet.',
                'permission' => 'DisbursementReport',
                'icon' => 'cash',
                'route' => '#',
                'reports' => [
                    'Disbursement Analysis',
                    'Disbursement by Account',
                    'Disbursement by BL',
                    'Revenue vs Expenditure',
                    'Unapproved Disbursements',
                    'Waste Sheet (Reversals)',
                ],
            ],
            [
                'title' => 'Accounting Reports',
                'description' => 'Daily balancing sheet, GL statements, trial balance, income & expenditure and balance sheet.',
                'permission' => 'AccountingReport',
                'icon' => 'book',
                'route' => '#',
                'reports' => [
                    'Daily Balancing Sheet',
                    'GL Account Statement',
                    'Trial Balance',
                    'Income & Expenditure',
                    'Balance Sheet',
                    'Receipt Register',
                    'Cash Flow Forecast',
                ],
            ],
            [
                'title' => 'Management Reports',
                'description' => 'Executive summary, financial performance, risk report and AI expense prediction.',
                'permission' => 'ManagementReport',
                'icon' => 'chart',
                'route' => '#',
                'reports' => [
                    'Executive Summary',
                    'Financial Performance',
                    'Risk Report',
                    'AI Expense Prediction',
                ],
            ],
        ];

        // Filter to only groups the user has permission for
        $visibleGroups = array_filter($groups, function ($group) use ($userAuth) {
            return $userAuth && $userAuth->hasPermission($group['permission']);
        });

        return view('reports.index', compact('visibleGroups'));
    }
}
