<x-filament-panels::page>
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                    <th class="py-2 px-3">Code</th>
                    <th class="py-2 px-3">Account</th>
                    <th class="py-2 px-3">Type</th>
                    <th class="py-2 px-3">Total Debit</th>
                    <th class="py-2 px-3">Total Credit</th>
                    <th class="py-2 px-3">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->getAccountsBalances() as $row)
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="py-2 px-3">{{ $row['code'] }}</td>
                        <td class="py-2 px-3">{{ $row['name'] }}</td>
                        <td class="py-2 px-3">{{ $row['type'] }}</td>
                        <td class="py-2 px-3">{{ number_format($row['debit'], 2) }}</td>
                        <td class="py-2 px-3">{{ number_format($row['credit'], 2) }}</td>
                        <td class="py-2 px-3 font-bold">{{ number_format($row['balance'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>