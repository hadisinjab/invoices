@props(['headers', 'rows', 'emptyMessage' => 'لا توجد بيانات'])

<div class="table-responsive">
    <!-- Desktop Table -->
    <table class="hidden md:table w-full bg-white rounded-lg shadow-lg overflow-hidden">
        <thead class="bg-gradient-to-r from-primary-500 to-secondary-500 text-white">
            <tr>
                @foreach($headers as $header)
                    <th class="px-6 py-4 text-right font-semibold text-sm uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($rows as $row)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    @foreach($row as $cell)
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $cell }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">{{ $emptyMessage }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        @forelse($rows as $index => $row)
            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200">
                @foreach($row as $cellIndex => $cell)
                    <div class="flex justify-between items-center py-2 {{ $cellIndex !== 0 ? 'border-t border-gray-100' : '' }}">
                        <span class="text-sm font-medium text-gray-600">
                            {{ $headers[$cellIndex] ?? '' }}
                        </span>
                        <span class="text-sm text-gray-900 text-left">
                            {{ $cell }}
                        </span>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                <p class="text-lg font-medium text-gray-500">{{ $emptyMessage }}</p>
            </div>
        @endforelse
    </div>
</div>
