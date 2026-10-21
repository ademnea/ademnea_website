@extends('layouts.app')
@section('content')

<style>
    .rs-page { max-width: 1180px; margin: 0 auto; padding: 28px 4px 40px; color: #1f2937; }

    .rs-header { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; }
    .rs-header-main { display: flex; align-items: center; gap: 14px; }
    .rs-header-icon { width: 48px; height: 48px; border-radius: 12px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; }
    .rs-title { font-size: 1.4rem; font-weight: 700; margin: 0; color: #111827; }
    .rs-subtitle { margin: 2px 0 0; color: #6b7280; font-size: 0.9rem; }

    .rs-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px; font-size: 0.78rem; font-weight: 600; }
    .rs-pill::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .rs-pill-ok { background: #dcfce7; color: #15803d; }
    .rs-pill-warn { background: #fef3c7; color: #b45309; }
    .rs-pill-off { background: #f3f4f6; color: #6b7280; }

    .rs-grid { display: grid; grid-template-columns: minmax(0, 1fr) 330px; gap: 24px; align-items: start; }
    @media (max-width: 991px) { .rs-grid { grid-template-columns: minmax(0, 1fr); } }

    .rs-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05); margin-bottom: 20px; }
    .rs-card-head { display: flex; align-items: flex-start; gap: 12px; padding: 18px 22px; border-bottom: 1px solid #f1f3f5; }
    .rs-card-head i { font-size: 20px; color: #15803d; background: #f0fdf4; border-radius: 8px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .rs-card-title { font-size: 1rem; font-weight: 600; margin: 0; color: #111827; }
    .rs-card-desc { margin: 2px 0 0; font-size: 0.83rem; color: #6b7280; }
    .rs-card-body { padding: 20px 22px; }

    .rs-row { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 16px; }
    .rs-col-4 { grid-column: span 4; } .rs-col-6 { grid-column: span 6; } .rs-col-8 { grid-column: span 8; } .rs-col-12 { grid-column: span 12; }
    @media (max-width: 640px) { .rs-col-4, .rs-col-6, .rs-col-8 { grid-column: span 12; } }
    .rs-row + .rs-row { margin-top: 16px; }

    .rs-label { display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .rs-label .rs-optional { font-weight: 400; color: #9ca3af; }
    .rs-label .rs-required { color: #dc2626; }
    .rs-input { width: 100%; height: 42px; padding: 0 12px; border: 1px solid #d1d5db; border-radius: 8px; background: #fff; font-size: 0.9rem; color: #111827; transition: border-color .15s, box-shadow .15s; }
    textarea.rs-input { height: auto; padding: 10px 12px; line-height: 1.5; resize: vertical; font-family: inherit; }
    .rs-input:focus { outline: none; border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15); }
    .rs-input::placeholder { color: #9ca3af; }
    .rs-hint { display: block; margin-top: 6px; font-size: 0.78rem; color: #6b7280; }

    .rs-add-row { display: flex; gap: 8px; }
    .rs-add-btn { width: 42px; padding: 0; flex-shrink: 0; }
    .rs-add-btn i { font-size: 22px; }
    .rs-input.rs-invalid { border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12); }
    .rs-field-error { display: block; margin-top: 6px; font-size: 0.78rem; color: #dc2626; }
    .rs-field-error[hidden] { display: none; }

    .rs-recipients-head { display: flex; align-items: center; gap: 8px; margin: 18px 0 8px; }
    .rs-count { min-width: 22px; height: 22px; padding: 0 7px; border-radius: 999px; background: #dcfce7; color: #15803d; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; }
    .rs-recipient-list { list-style: none; margin: 0; padding: 0; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .rs-recipient-list:empty { display: none; }
    .rs-recipient { display: flex; align-items: center; gap: 12px; padding: 8px 8px 8px 12px; border-bottom: 1px solid #f1f3f5; background: #fff; transition: background .15s; }
    .rs-recipient:last-child { border-bottom: 0; }
    .rs-recipient:hover { background: #f9fafb; }
    .rs-recipient-avatar { width: 30px; height: 30px; border-radius: 50%; background: #f0fdf4; color: #15803d; font-size: 0.8rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .rs-recipient-email { flex: 1; min-width: 0; font-size: 0.88rem; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .rs-recipient-remove { width: 34px; height: 34px; border: 0; border-radius: 8px; background: transparent; color: #9ca3af; font-size: 18px; cursor: pointer; flex-shrink: 0; transition: background .15s, color .15s; }
    .rs-recipient-remove:hover, .rs-recipient-remove:focus-visible { background: #fee2e2; color: #dc2626; outline: none; }
    .rs-recipient-new { animation: rs-flash 1.2s ease-out; }
    @keyframes rs-flash { from { background: #dcfce7; } to { background: #fff; } }
    .rs-empty { display: flex; align-items: center; gap: 8px; margin: 0; padding: 14px; border: 1px dashed #d1d5db; border-radius: 10px; font-size: 0.83rem; color: #6b7280; }
    .rs-empty[hidden] { display: none; }
    .rs-empty i { font-size: 20px; color: #9ca3af; }

    .rs-password { position: relative; }
    .rs-password .rs-input { padding-right: 42px; }
    .rs-password-toggle { position: absolute; top: 0; right: 0; width: 42px; height: 42px; border: 0; background: transparent; color: #6b7280; font-size: 18px; cursor: pointer; }
    .rs-password-toggle:hover { color: #111827; }


    .rs-switch { display: flex; align-items: flex-start; gap: 12px; cursor: pointer; margin: 0; }
    .rs-switch input { position: absolute; opacity: 0; }
    .rs-switch-track { width: 42px; height: 24px; border-radius: 999px; background: #d1d5db; position: relative; flex-shrink: 0; transition: background .15s; margin-top: 2px; }
    .rs-switch-track::after { content: ''; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.25); transition: transform .15s; }
    .rs-switch input:checked + .rs-switch-track { background: #16a34a; }
    .rs-switch input:checked + .rs-switch-track::after { transform: translateX(18px); }
    .rs-switch input:focus-visible + .rs-switch-track { box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.25); }
    .rs-switch-text strong { display: block; font-size: 0.9rem; color: #111827; }
    .rs-switch-text span { font-size: 0.8rem; color: #6b7280; }

    .rs-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 42px; padding: 0 18px; border-radius: 8px; border: 1px solid transparent; font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: background .15s, border-color .15s, color .15s; text-decoration: none !important; white-space: nowrap; }
    .rs-btn i { font-size: 18px; }
    .rs-btn:disabled { opacity: .6; cursor: not-allowed; }
    .rs-btn-primary { background: #15803d; color: #fff; }
    .rs-btn-primary:hover { background: #166534; color: #fff; }
    .rs-btn-outline { background: #fff; color: #374151; border-color: #d1d5db; }
    .rs-btn-outline:hover { background: #f9fafb; border-color: #9ca3af; color: #111827; }
    .rs-btn-danger { background: #dc2626; color: #fff; }
    .rs-btn-danger:hover { background: #b91c1c; color: #fff; }
    .rs-btn-block { width: 100%; }

    .rs-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 12px; }

    .rs-spinner { width: 16px; height: 16px; border: 2px solid currentColor; border-right-color: transparent; border-radius: 50%; animation: rs-spin .7s linear infinite; }
    @keyframes rs-spin { to { transform: rotate(360deg); } }

    .rs-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 10px; font-size: 0.88rem; margin-bottom: 20px; border: 1px solid; }
    .rs-alert i { font-size: 20px; line-height: 1.1; }
    .rs-alert ul { margin: 0; padding-left: 18px; }
    .rs-alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
    .rs-alert-error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }

    .rs-side { position: sticky; top: 90px; }
    .rs-stat { display: flex; justify-content: space-between; gap: 12px; padding: 10px 0; font-size: 0.85rem; border-bottom: 1px solid #f1f3f5; }
    .rs-stat:last-child { border-bottom: 0; padding-bottom: 0; }
    .rs-stat:first-child { padding-top: 0; }
    .rs-stat dt { color: #6b7280; font-weight: 400; margin: 0; }
    .rs-stat dd { color: #111827; font-weight: 600; margin: 0; text-align: right; word-break: break-word; }
    .rs-stack > * + * { margin-top: 10px; }
    .rs-note { font-size: 0.8rem; color: #6b7280; margin: 0 0 14px; }

    .rs-modal { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(17, 24, 39, 0.55); }
    .rs-modal[hidden] { display: none; }
    .rs-modal-box { width: 100%; max-width: 420px; background: #fff; border-radius: 16px; padding: 28px 26px 22px; text-align: center; box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25); animation: rs-pop .18s ease-out; }
    @keyframes rs-pop { from { transform: scale(.94); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .rs-modal-icon { width: 64px; height: 64px; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 34px; }
    .rs-modal-success .rs-modal-icon { background: #dcfce7; color: #16a34a; }
    .rs-modal-failure .rs-modal-icon { background: #fee2e2; color: #dc2626; }
    .rs-modal-title { font-size: 1.15rem; font-weight: 700; color: #111827; margin: 0 0 8px; }
    .rs-modal-message { font-size: 0.88rem; color: #4b5563; margin: 0 0 22px; word-break: break-word; }
</style>

@php
    $isConfigured = $settings->isConfigured();
    $recipientCount = count($settings->recipients ?? []);
    $encryption = old('smtp_encryption', $settings->smtp_encryption);
@endphp

<div class="rs-page">

    <div class="rs-header">
        <div class="rs-header-main">
            <div class="rs-header-icon"><i class="mdi mdi-email-outline"></i></div>
            <div>
                <h1 class="rs-title">Report Email Settings</h1>
                <p class="rs-subtitle">Mail server and recipients for the hives-needing-attention report.</p>
            </div>
        </div>
        <div class="rs-header-main">
            @if($isConfigured)
                <span class="rs-pill rs-pill-ok">Configured</span>
            @else
                <span class="rs-pill rs-pill-warn">Setup incomplete</span>
            @endif
            <a href="{{ url('/admin/dashboard/hives?status=attention') }}" class="rs-btn rs-btn-outline">
                <i class="mdi mdi-alert-circle-outline"></i> Hives needing attention
            </a>
        </div>
    </div>

    @if(session('flash_message'))
        <div class="rs-alert rs-alert-success"><i class="mdi mdi-check-circle-outline"></i><div>{{ session('flash_message') }}</div></div>
    @endif
    @if(session('flash_error'))
        <div class="rs-alert rs-alert-error"><i class="mdi mdi-alert-circle-outline"></i><div>{{ session('flash_error') }}</div></div>
    @endif
    @if($errors->any())
        <div class="rs-alert rs-alert-error">
            <i class="mdi mdi-alert-circle-outline"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rs-grid">

        <form id="reportSettingsForm" action="{{ url('/admin/report-settings') }}" method="POST" autocomplete="off">
            {{ csrf_field() }}

            <div class="rs-card">
                <div class="rs-card-head">
                    <i class="mdi mdi-server-network"></i>
                    <div>
                        <h2 class="rs-card-title">SMTP server</h2>
                        <p class="rs-card-desc">The outgoing mail server used to deliver reports.</p>
                    </div>
                </div>
                <div class="rs-card-body">
                    <div class="rs-row">
                        <div class="rs-col-6">
                            <label class="rs-label" for="smtp_host">Host</label>
                            <input type="text" class="rs-input" id="smtp_host" name="smtp_host"
                                   value="{{ old('smtp_host', $settings->smtp_host) }}" placeholder="smtp.example.com" required>
                        </div>
                        <div class="rs-col-6" style="display: grid; grid-template-columns: 1fr 1.4fr; gap: 16px;">
                            <div>
                                <label class="rs-label" for="smtp_port">Port</label>
                                <input type="number" class="rs-input" id="smtp_port" name="smtp_port" min="1" max="65535"
                                       value="{{ old('smtp_port', $settings->smtp_port) }}" placeholder="587" required>
                            </div>
                            <div>
                                <label class="rs-label" for="smtp_encryption">Encryption</label>
                                <select class="rs-input" id="smtp_encryption" name="smtp_encryption">
                                    <option value="tls" {{ $encryption === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ $encryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ empty($encryption) ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="rs-row">
                        <div class="rs-col-6">
                            <label class="rs-label" for="smtp_username">Email address <span class="rs-required">*</span></label>
                            <input type="email" class="rs-input" id="smtp_username" name="smtp_username"
                                   value="{{ old('smtp_username', $settings->smtp_username) }}"
                                   placeholder="reports@gmail.com" autocomplete="off" required>
                            <span class="rs-hint">The email account the reports are sent from. Used to sign in to the mail server.</span>
                        </div>
                        <div class="rs-col-6">
                            <label class="rs-label" for="smtp_password">App password <span class="rs-required">*</span></label>
                            <div class="rs-password">
                                <input type="password" class="rs-input" id="smtp_password" name="smtp_password"
                                       autocomplete="new-password"
                                       placeholder="{{ $settings->smtp_password ? '•••••••• saved' : 'xxxx xxxx xxxx xxxx' }}"
                                       {{ $settings->smtp_password ? '' : 'required' }}>
                                <button type="button" class="rs-password-toggle" id="togglePassword" aria-label="Show password">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                            @if($settings->smtp_password)
                                <span class="rs-hint">An app password is saved. Leave blank to keep it, or enter a new one to replace it.</span>
                            @else
                                <span class="rs-hint">The app password generated in the email account's security settings, not the normal login password.</span>
                            @endif
                        </div>
                    </div>
                    <span class="rs-hint">TLS normally uses port 587 and SSL port 465.</span>
                </div>
            </div>

            <div class="rs-card">
                <div class="rs-card-head">
                    <i class="mdi mdi-account-outline"></i>
                    <div>
                        <h2 class="rs-card-title">Sender</h2>
                        <p class="rs-card-desc">How the report appears in the recipient's inbox.</p>
                    </div>
                </div>
                <div class="rs-card-body">
                    <div class="rs-row">
                        <div class="rs-col-6">
                            <label class="rs-label" for="from_address">From address</label>
                            <input type="email" class="rs-input" id="from_address" name="from_address"
                                   value="{{ old('from_address', $settings->from_address) }}" placeholder="reports@example.com" required>
                        </div>
                        <div class="rs-col-6">
                            <label class="rs-label" for="from_name">From name <span class="rs-optional">(optional)</span></label>
                            <input type="text" class="rs-input" id="from_name" name="from_name"
                                   value="{{ old('from_name', $settings->from_name) }}" placeholder="AdeMNEA Reports">
                        </div>
                    </div>
                </div>
            </div>

            <div class="rs-card">
                <div class="rs-card-head">
                    <i class="mdi mdi-account-multiple-outline"></i>
                    <div>
                        <h2 class="rs-card-title">Recipients</h2>
                        <p class="rs-card-desc">Anyone who should receive the report. They do not have to be farmers.</p>
                    </div>
                </div>
                <div class="rs-card-body">
                    @php
                        $recipientList = old('recipients', $settings->recipients ?? []);
                        if (!is_array($recipientList)) {
                            $recipientList = preg_split('/[\s,;]+/', (string) $recipientList, -1, PREG_SPLIT_NO_EMPTY);
                        }
                    @endphp

                    <label class="rs-label" for="recipientInput">Add an email address</label>
                    <div class="rs-add-row">
                        <input type="email" class="rs-input" id="recipientInput" placeholder="name@example.com" autocomplete="off">
                        <button type="button" class="rs-btn rs-btn-primary rs-add-btn" id="addRecipientBtn" aria-label="Add recipient" title="Add recipient">
                            <i class="mdi mdi-plus"></i>
                        </button>
                    </div>
                    <span class="rs-field-error" id="recipientError" role="alert" hidden></span>

                    <div class="rs-recipients-head">
                        <span class="rs-label" style="margin: 0;">Recipients</span>
                        <span class="rs-count" id="recipientCount">{{ count($recipientList) }}</span>
                    </div>

                    <ul class="rs-recipient-list" id="recipientList">
                        @foreach($recipientList as $email)
                            <li class="rs-recipient">
                                <span class="rs-recipient-avatar">{{ strtoupper(substr($email, 0, 1)) }}</span>
                                <span class="rs-recipient-email">{{ $email }}</span>
                                <input type="hidden" name="recipients[]" value="{{ $email }}">
                                <button type="button" class="rs-recipient-remove" aria-label="Remove {{ $email }}" title="Remove">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <p class="rs-empty" id="recipientEmpty" {{ count($recipientList) ? 'hidden' : '' }}>
                        <i class="mdi mdi-email-plus-outline"></i>
                        No recipients yet. Type an email address above and press <strong>+</strong>.
                    </p>
                    <span class="rs-hint">Press Enter or the + button to add each address. Click Save settings to keep your changes.</span>
                </div>
            </div>

            <div class="rs-card">
                <div class="rs-card-head">
                    <i class="mdi mdi-clock-outline"></i>
                    <div>
                        <h2 class="rs-card-title">Schedule</h2>
                        <p class="rs-card-desc">Optionally send the report automatically once a day.</p>
                    </div>
                </div>
                <div class="rs-card-body">
                    <div class="rs-row" style="align-items: center;">
                        <div class="rs-col-8">
                            <label class="rs-switch">
                                <input type="checkbox" name="auto_send" value="1" {{ old('auto_send', $settings->auto_send) ? 'checked' : '' }}>
                                <span class="rs-switch-track"></span>
                                <span class="rs-switch-text">
                                    <strong>Send automatically every day</strong>
                                    <span>Only sent on days when at least one hive needs attention.</span>
                                </span>
                            </label>
                        </div>
                        <div class="rs-col-4">
                            <label class="rs-label" for="send_time">Send time</label>
                            <input type="time" class="rs-input" id="send_time" name="send_time"
                                   value="{{ old('send_time', $settings->send_time) }}" required>
                            <span class="rs-hint">Server time ({{ config('app.timezone') }}), now {{ now()->format('H:i') }}.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rs-actions">
                <button type="button" class="rs-btn rs-btn-outline" id="testConnectionBtn"
                        data-url="{{ url('/admin/report-settings/test-connection') }}">
                    <i class="mdi mdi-lan-connect"></i> <span>Test connection</span>
                </button>
                <button type="submit" class="rs-btn rs-btn-primary">
                    <i class="mdi mdi-content-save-outline"></i> Save settings
                </button>
            </div>
        </form>

        <aside class="rs-side">
            <div class="rs-card">
                <div class="rs-card-head">
                    <i class="mdi mdi-information-outline"></i>
                    <div>
                        <h2 class="rs-card-title">Status</h2>
                        <p class="rs-card-desc">Based on the saved settings.</p>
                    </div>
                </div>
                <div class="rs-card-body">
                    <dl style="margin: 0;">
                        <div class="rs-stat">
                            <dt>Server</dt>
                            <dd>{{ $settings->smtp_host ? $settings->smtp_host . ':' . $settings->smtp_port : 'Not set' }}</dd>
                        </div>
                        <div class="rs-stat">
                            <dt>Recipients</dt>
                            <dd>{{ $recipientCount }}</dd>
                        </div>
                        <div class="rs-stat">
                            <dt>Daily send</dt>
                            <dd>
                                @if($settings->auto_send)
                                    <span class="rs-pill rs-pill-ok">On at {{ $settings->send_time }}</span>
                                @else
                                    <span class="rs-pill rs-pill-off">Off</span>
                                @endif
                            </dd>
                        </div>
                        <div class="rs-stat">
                            <dt>Last report</dt>
                            <dd>{{ $settings->last_sent_at ? $settings->last_sent_at->diffForHumans() : 'Never' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="rs-card">
                <div class="rs-card-head">
                    <i class="mdi mdi-send"></i>
                    <div>
                        <h2 class="rs-card-title">Send</h2>
                        <p class="rs-card-desc">Deliver to the saved recipients.</p>
                    </div>
                </div>
                <div class="rs-card-body">
                    @if($isConfigured)
                        <p class="rs-note">Uses the saved settings, so save any changes first.</p>
                        <div class="rs-stack">
                            <form action="{{ url('/admin/report-settings/test') }}" method="POST" class="rs-send-form">
                                {{ csrf_field() }}
                                <button type="submit" class="rs-btn rs-btn-outline rs-btn-block">
                                    <i class="mdi mdi-email-check-outline"></i> Send test email
                                </button>
                            </form>
                            <form action="{{ url('/admin/report-settings/send') }}" method="POST" class="rs-send-form">
                                {{ csrf_field() }}
                                <button type="submit" class="rs-btn rs-btn-danger rs-btn-block">
                                    <i class="mdi mdi-send"></i> Send report now
                                </button>
                            </form>
                        </div>
                    @else
                        <p class="rs-note" style="margin: 0;">Save the SMTP server, the sign-in email and app password, a from address and at least one recipient to enable sending.</p>
                    @endif
                </div>
            </div>
        </aside>

    </div>
</div>

<!-- Test connection result popup -->
<div class="rs-modal" id="connectionModal" hidden role="dialog" aria-modal="true" aria-labelledby="connectionModalTitle">
    <div class="rs-modal-box" id="connectionModalBox">
        <div class="rs-modal-icon"><i class="mdi" id="connectionModalIcon"></i></div>
        <h3 class="rs-modal-title" id="connectionModalTitle"></h3>
        <p class="rs-modal-message" id="connectionModalMessage"></p>
        <button type="button" class="rs-btn rs-btn-primary rs-btn-block" id="connectionModalClose">OK</button>
    </div>
</div>

@endsection

@section('page_scripts')
<script>
    (function () {
        var form = document.getElementById('reportSettingsForm');
        var testBtn = document.getElementById('testConnectionBtn');
        var modal = document.getElementById('connectionModal');
        var modalBox = document.getElementById('connectionModalBox');
        var modalIcon = document.getElementById('connectionModalIcon');
        var modalTitle = document.getElementById('connectionModalTitle');
        var modalMessage = document.getElementById('connectionModalMessage');
        var modalClose = document.getElementById('connectionModalClose');

        function showResult(success, message) {
            modalBox.className = 'rs-modal-box ' + (success ? 'rs-modal-success' : 'rs-modal-failure');
            modalIcon.className = 'mdi ' + (success ? 'mdi-check' : 'mdi-close');
            modalTitle.textContent = success ? 'Test connection successful' : 'Test connection failed';
            modalMessage.textContent = message || '';
            modal.hidden = false;
            modalClose.focus();
        }

        function closeModal() {
            modal.hidden = true;
            testBtn.focus();
        }

        modalClose.addEventListener('click', closeModal);
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hidden) closeModal();
        });

        testBtn.addEventListener('click', function () {
            var icon = testBtn.querySelector('i');
            var label = testBtn.querySelector('span');

            testBtn.disabled = true;
            icon.className = 'rs-spinner';
            label.textContent = 'Testing...';

            fetch(testBtn.dataset.url, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                body: new FormData(form)
            })
                .then(function (response) {
                    if (response.status === 419 || response.status === 401) {
                        throw new Error('Your session has expired. Refresh the page and sign in again.');
                    }
                    if (!response.ok) {
                        throw new Error('The server could not run the test (error ' + response.status + ').');
                    }
                    return response.json();
                })
                .then(function (data) {
                    showResult(!!data.success, data.message);
                })
                .catch(function (error) {
                    showResult(false, error.message);
                })
                .then(function () {
                    testBtn.disabled = false;
                    icon.className = 'mdi mdi-lan-connect';
                    label.textContent = 'Test connection';
                });
        });

        // ---- Recipients: add one at a time, remove any ----
        var recipientInput = document.getElementById('recipientInput');
        var recipientList = document.getElementById('recipientList');
        var recipientEmpty = document.getElementById('recipientEmpty');
        var recipientCount = document.getElementById('recipientCount');
        var recipientError = document.getElementById('recipientError');
        var emailPattern = /^[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+$/;

        function currentRecipients() {
            return Array.prototype.map.call(recipientList.querySelectorAll('input[name="recipients[]"]'), function (input) {
                return input.value.toLowerCase();
            });
        }

        function refreshRecipients() {
            var count = recipientList.children.length;
            recipientCount.textContent = count;
            recipientEmpty.hidden = count > 0;
        }

        function setRecipientError(message) {
            recipientError.textContent = message || '';
            recipientError.hidden = !message;
            recipientInput.classList.toggle('rs-invalid', !!message);
        }

        function addRecipient() {
            var email = recipientInput.value.trim().toLowerCase();

            if (email === '') {
                setRecipientError('Type an email address first.');
                recipientInput.focus();
                return false;
            }
            if (!emailPattern.test(email)) {
                setRecipientError('"' + email + '" is not a valid email address.');
                recipientInput.focus();
                return false;
            }
            if (currentRecipients().indexOf(email) !== -1) {
                setRecipientError(email + ' is already in the list.');
                recipientInput.focus();
                return false;
            }

            var item = document.createElement('li');
            item.className = 'rs-recipient rs-recipient-new';

            var avatar = document.createElement('span');
            avatar.className = 'rs-recipient-avatar';
            avatar.textContent = email.charAt(0).toUpperCase();

            var text = document.createElement('span');
            text.className = 'rs-recipient-email';
            text.textContent = email;

            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'recipients[]';
            hidden.value = email;

            var remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'rs-recipient-remove';
            remove.title = 'Remove';
            remove.setAttribute('aria-label', 'Remove ' + email);
            remove.innerHTML = '<i class="mdi mdi-trash-can-outline"></i>';

            item.appendChild(avatar);
            item.appendChild(text);
            item.appendChild(hidden);
            item.appendChild(remove);
            recipientList.appendChild(item);

            recipientInput.value = '';
            setRecipientError('');
            refreshRecipients();
            recipientInput.focus();
            return true;
        }

        document.getElementById('addRecipientBtn').addEventListener('click', addRecipient);

        recipientInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // add the address instead of submitting the form
                addRecipient();
            }
        });
        recipientInput.addEventListener('input', function () {
            if (!recipientError.hidden) setRecipientError('');
        });

        recipientList.addEventListener('click', function (e) {
            var button = e.target.closest('.rs-recipient-remove');
            if (!button) return;
            button.closest('.rs-recipient').remove();
            refreshRecipients();
            recipientInput.focus();
        });

        form.addEventListener('submit', function (e) {
            // An address typed but not yet added with + still counts.
            if (recipientInput.value.trim() !== '' && !addRecipient()) {
                e.preventDefault();
                return;
            }
            if (recipientList.children.length === 0) {
                e.preventDefault();
                setRecipientError('Add at least one recipient before saving.');
                recipientInput.focus();
            }
        });

        document.getElementById('togglePassword').addEventListener('click', function () {
            var input = document.getElementById('smtp_password');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            this.querySelector('i').className = 'mdi ' + (show ? 'mdi-eye-off-outline' : 'mdi-eye-outline');
            this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });

        // Sending can take a few seconds - stop double clicks and show progress.
        document.querySelectorAll('.rs-send-form').forEach(function (sendForm) {
            sendForm.addEventListener('submit', function () {
                var button = sendForm.querySelector('button');
                button.disabled = true;
                button.querySelector('i').className = 'rs-spinner';
            });
        });
    })();
</script>
@endsection
