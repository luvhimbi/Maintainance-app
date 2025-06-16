@extends('layouts.mail')

@section('content')
<h1 style="color: #333333; font-family: sans-serif; font-weight: 300; line-height: 1.4; margin: 0; margin-bottom: 30px; font-size: 35px; text-align: center; text-transform: capitalize;">Issue Update Notification</h1>

<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Dear {{ $issue->reporter->first_name }},</p>

<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Your reported issue has been updated.</p>

<h2 style="color: #333333; font-family: sans-serif; font-weight: 300; line-height: 1.4; margin: 0; margin-bottom: 15px; font-size: 20px;">Issue Details:</h2>
<ul style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px; padding-left: 20px;">
    <li>Issue ID: #{{ $issue->issue_id }}</li>
    <li>Type: {{ $issue->issue_type }}</li>
    <li>Location: {{ $location }}</li>
    <li>Status: {{ $update->status_change }}</li>
</ul>

<h2 style="color: #333333; font-family: sans-serif; font-weight: 300; line-height: 1.4; margin: 0; margin-bottom: 15px; font-size: 20px;">Update Details:</h2>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">{{ $update->update_description }}</p>

<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Updated by:</strong> {{ $updater->first_name }} {{ $updater->last_name }}</p>

<table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
    <tbody>
        <tr>
            <td align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                    <tbody>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;">
                                <a href="{{ route('Student.issue_details', $issue->issue_id) }}" target="_blank" style="display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 24px; text-transform: capitalize; border-color: #3498db;">View Issue Details</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Thank you for using our maintenance system.</p>

<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Best regards,<br>{{ config('app.name') }}</p>
@endsection 