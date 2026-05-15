@extends('layouts.admin')

@section('page-title', 'Activity Logs')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Activity Logs</h2>
            <p class="text-sm text-gray-500 mt-0.5">Audit trail of all actions performed in the system.</p>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label for="action" class="block text-xs font-medium text-gray-600 mb-1">Filter by Action</label>
                <select id="action" name="action"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="">All actions</option>
                    @if(isset($actions))
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    @else
                        <option value="create"  {{ request('action') === 'create'  ? 'selected' : '' }}>Create</option>
                        <option value="update"  {{ request('action') === 'update'  ? 'selected' : '' }}>Update</option>
                        <option value="delete"  {{ request('action') === 'delete'  ? 'selected' : '' }}>Delete</option>
                        <option value="login"   {{ request('action') === 'login'   ? 'selected' : '' }}>Login</option>
                        <option value="logout"  {{ request('action') === 'logout'  ? 'selected' : '' }}>Logout</option>
                        <option value="upload"  {{ request('action') === 'upload'  ? 'selected' : '' }}>Upload</option>
                        <option value="download"{{ request('action') === 'download'? 'selected' : '' }}>Download</option>
                    @endif
                </select>
            </div>
            <div>
                <label for="user_id" class="block text-xs font-medium text-gray-600 mb-1">Filter by User</label>
                <select id="user_id" name="user_id"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="">All users</option>
                    @if(isset($users))
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    Filter
                </button>
                @if(request('action') || request('user_id'))
                    <a href="{{ route('admin.activity-logs.index') }}"
                       class="px-4 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if(isset($logs) && $logs->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Action</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3">IP Address</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $log->user->name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $actionColors = [
                                            'create'   => 'bg-green-100 text-green-800',
                                            'update'   => 'bg-blue-100 text-blue-800',
                                            'delete'   => 'bg-red-100 text-red-800',
                                            'login'    => 'bg-indigo-100 text-indigo-800',
                                            'logout'   => 'bg-gray-100 text-gray-700',
                                            'upload'   => 'bg-purple-100 text-purple-800',
                                            'download' => 'bg-cyan-100 text-cyan-800',
                                        ];
                                        $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $colorClass }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 max-w-xs">
                                    <p class="truncate">{{ $log->description }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $log->ip_address ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="font-medium">No activity logs found.</p>
                @if(request()->hasAny(['action', 'user_id']))
                    <p class="text-sm mt-1">Try clearing the filters.</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
