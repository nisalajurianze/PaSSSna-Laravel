@extends('layouts.app')

@section('title', 'Book a Table - PaSSSna Restaurant')

@section('styles')
<style>
    .table-available {
        animation: pulse-available 2s infinite;
    }
    @keyframes pulse-available {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
    }
    .table-selected {
        transform: scale(1.05);
        box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
    }
    .time-slot {
        transition: all 0.2s ease;
    }
    .time-slot:hover {
        transform: translateY(-2px);
    }
    .time-slot.selected {
        background: linear-gradient(135deg, #DC2626, #FBBF24);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-6xl mx-auto">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-gray-800 mb-4 animate-fade-in">Reserve Your Table</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Experience fine dining at PaSSSna. Book your table online for a memorable culinary journey.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Reservation Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 animate-slide-up">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Reservation Details</h2>

                    <form id="reservationForm" action="{{ route('reservation.store') }}" method="POST">
                        @csrf

                        <!-- Personal Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-gray-700 mb-2 font-semibold">Full Name *</label>
                                <input type="text"
                                       name="name"
                                       value="{{ auth()->check() ? auth()->user()->name : '' }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                                       required>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2 font-semibold">Email Address *</label>
                                <input type="email"
                                       name="email"
                                       value="{{ auth()->check() ? auth()->user()->email : '' }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                                       required>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2 font-semibold">Phone Number *</label>
                                <input type="tel"
                                       name="phone"
                                       value="{{ auth()->check() ? auth()->user()->phone : '' }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                                       required>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2 font-semibold">Number of Guests *</label>
                                <select name="guests"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                                        required>
                                    <option value="">Select guests</option>
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'person' : 'people' }}</option>
                                    @endfor
                                    <option value="13">13+ people (Contact us)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-gray-700 mb-2 font-semibold">Reservation Date *</label>
                                <input type="date"
                                       name="date"
                                       min="{{ date('Y-m-d') }}"
                                       max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                                       required
                                       onchange="loadAvailableTimes()">
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2 font-semibold">Preferred Time *</label>
                                <div id="timeSlots" class="grid grid-cols-4 gap-2">
                                    <!-- Time slots will be loaded via JavaScript -->
                                    <div class="col-span-4 text-center py-4">
                                        <p class="text-gray-600">Select a date first</p>
                                    </div>
                                </div>
                                <input type="hidden" name="time" id="selectedTime" required>
                            </div>
                        </div>

                        <!-- Table Selection -->
                        <div class="mb-8">
                            <label class="block text-gray-700 mb-4 font-semibold">Select Table(s)</label>
                            <div class="mb-4">
                                <div class="flex items-center space-x-4 mb-2">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-green-500 rounded-full mr-2"></div>
                                        <span class="text-sm">Available</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-red-500 rounded-full mr-2"></div>
                                        <span class="text-sm">Reserved</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-yellow-500 rounded-full mr-2"></div>
                                        <span class="text-sm">Selected</span>
                                    </div>
                                </div>
                            </div>

                            <div id="tablesGrid" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($availableTables as $table)
                                @php $isAvailable = $table->status === 'available'; @endphp
                                <div class="table-item relative"
                                     data-table-id="{{ $table->id }}"
                                     data-table-number="{{ $table->table_number }}"
                                     data-capacity="{{ $table->capacity }}"
                                     data-base-available="{{ $isAvailable ? 1 : 0 }}">
                                    <div class="table-card border-2 border-gray-300 rounded-xl p-4 text-center transition-all duration-300
                                        {{ $isAvailable ? 'cursor-pointer hover:border-green-500' : 'opacity-50 cursor-not-allowed' }}">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2
                                            {{ $isAvailable ? 'bg-green-100' : 'bg-red-100' }}">
                                            <i class="fas fa-chair text-2xl {{ $isAvailable ? 'text-green-600' : 'text-red-600' }}"></i>
                                        </div>
                                        <h4 class="font-semibold text-gray-800">Table {{ $table->number }}</h4>
                                        <p class="text-sm text-gray-600">{{ $table->capacity }} Persons</p>
                                        <p class="table-availability-label text-xs mt-1 {{ $isAvailable ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $isAvailable ? 'Available' : 'Reserved' }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Selected tables summary + hidden inputs -->
                            <div class="mt-4 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Selected tables</span>
                                    <span id="selectedTablesLabel" class="font-semibold text-gray-800">None</span>
                                </div>
                                <div class="flex justify-between text-sm mt-2">
                                    <span class="text-gray-600">Total capacity</span>
                                    <span id="selectedCapacityLabel" class="font-semibold text-gray-800">0</span>
                                </div>
                                <p id="capacityHint" class="text-xs text-gray-500 mt-2">Tip: You can select multiple tables for larger groups.</p>
                            </div>
                            <div id="selectedTablesInputs"></div>

                            <p class="text-sm text-gray-600 mt-2">Select one or more available tables that fit your party size.</p>
                        </div>

                        <!-- Special Requests -->
                        <div class="mb-8">
                            <label class="block text-gray-700 mb-2 font-semibold">Special Requests (Optional)</label>
                            <textarea name="special_requests"
                                      rows="4"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                                      placeholder="Any special occasion, dietary requirements, or preferences?"></textarea>
                        </div>

                        <!-- Terms & Submit -->
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <label class="flex items-start mb-4 md:mb-0">
                                <input type="checkbox"
                                       name="terms"
                                       class="mt-1 mr-3"
                                       required>
                                <span class="text-sm text-gray-600">
                                    I agree to the <a href="#" class="text-primary-red hover:underline">Reservation Policy</a>
                                </span>
                            </label>
                            <button type="submit"
                                    class="bg-gradient-to-r from-primary-red to-primary-yellow text-white px-10 py-4 rounded-lg font-semibold hover:opacity-90 transition duration-300 transform hover:scale-105">
                                <i class="fas fa-calendar-check mr-2"></i>Confirm Reservation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="lg:col-span-1">
                <!-- Restaurant Hours -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 animate-slide-up" style="animation-delay: 0.1s">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-clock text-primary-red mr-3"></i>
                        Opening Hours
                    </h3>
                    <div class="space-y-4">
                        @foreach([
                            ['day' => 'Monday - Thursday', 'hours' => '11:00 AM - 10:00 PM'],
                            ['day' => 'Friday - Saturday', 'hours' => '11:00 AM - 11:00 PM'],
                            ['day' => 'Sunday', 'hours' => '12:00 PM - 9:00 PM'],
                            ['day' => 'Special Holidays', 'hours' => '12:00 PM - 8:00 PM']
                        ] as $schedule)
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $schedule['day'] }}</span>
                            <span class="font-semibold text-gray-800">{{ $schedule['hours'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Why Reserve -->
                <div class="bg-gradient-to-br from-navy-blue to-blue-900 rounded-2xl shadow-xl p-6 mb-8 text-white animate-slide-up" style="animation-delay: 0.2s">
                    <h3 class="text-2xl font-bold mb-6">Why Reserve With Us?</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-star text-primary-yellow mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold mb-1">Guaranteed Seating</h4>
                                <p class="text-blue-100 text-sm">Skip the wait and enjoy immediate seating upon arrival.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-gift text-primary-yellow mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold mb-1">Special Occasions</h4>
                                <p class="text-blue-100 text-sm">We prepare special arrangements for birthdays and anniversaries.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-crown text-primary-yellow mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold mb-1">Priority Service</h4>
                                <p class="text-blue-100 text-sm">Reserved guests receive priority attention from our staff.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Contact -->
                <div class="bg-white rounded-2xl shadow-xl p-6 animate-slide-up" style="animation-delay: 0.3s">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Need Assistance?</h3>
                    <div class="space-y-4">
                        <a href="tel:+15551234567"
                           class="flex items-center justify-center bg-primary-red text-white px-6 py-4 rounded-lg hover:bg-red-700 transition duration-300">
                            <i class="fas fa-phone-alt mr-3"></i>
                            Call: +1 (555) 123-4567
                        </a>
                        <a href="mailto:reservations@passsna.com"
                           class="flex items-center justify-center bg-gray-800 text-white px-6 py-4 rounded-lg hover:bg-gray-900 transition duration-300">
                            <i class="fas fa-envelope mr-3"></i>
                            Email Reservations
                        </a>
                    </div>
                    <div class="mt-6 p-4 bg-gradient-to-r from-yellow-50 to-red-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-info-circle text-primary-red mr-2"></i>
                            For large groups (13+ people), please contact us directly for special arrangements.
                        </p>
                    </div>
                </div>

                <!-- Reservation Policy -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mt-8 animate-slide-up" style="animation-delay: 0.4s">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Reservation Policy</h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <p>Reservations are held for 15 minutes past the scheduled time.</p>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <p>Cancellations must be made at least 2 hours in advance.</p>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <p>Late arrivals may result in reduced table holding time.</p>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <p>Special requests are subject to availability.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupTableSelection();
        setupTimeSlotSelection();
        loadAvailableTimes();
        setupGuestChange();
    });

    function setupTableSelection() {
        const tables = document.querySelectorAll('.table-item');
        const selectedIds = new Set();

        const syncHiddenInputs = () => {
            const container = document.getElementById('selectedTablesInputs');
            container.innerHTML = '';
            [...selectedIds].forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'tables[]';
                input.value = id;
                container.appendChild(input);
            });
        };

        const updateSummary = () => {
            const label = document.getElementById('selectedTablesLabel');
            const capLabel = document.getElementById('selectedCapacityLabel');
            const hint = document.getElementById('capacityHint');
            const guests = parseInt(document.querySelector('select[name="guests"]').value || '0');

            if (selectedIds.size === 0) {
                label.textContent = 'None';
                capLabel.textContent = '0';
                hint.textContent = 'Tip: You can select multiple tables for larger groups.';
                hint.className = 'text-xs text-gray-500 mt-2';
                return;
            }

            const selectedEls = [...selectedIds].map(id => document.querySelector(`[data-table-id="${id}"]`));
            const tableNumbers = selectedEls
                .map(el => el?.dataset?.tableNumber)
                .filter(Boolean);
            const totalCap = selectedEls
                .map(el => parseInt(el?.dataset?.capacity || '0'))
                .reduce((a, b) => a + b, 0);

            label.textContent = tableNumbers.length ? tableNumbers.join(', ') : `${selectedIds.size} table(s)`;
            capLabel.textContent = totalCap.toString();

            if (guests > 0 && totalCap < guests) {
                hint.textContent = `Selected capacity (${totalCap}) is less than guests (${guests}). Select more tables or reduce guests.`;
                hint.className = 'text-xs text-red-600 mt-2';
            } else {
                hint.textContent = 'Looks good!';
                hint.className = 'text-xs text-green-700 mt-2';
            }
        };

        const clearUnavailableSelections = () => {
            [...selectedIds].forEach(id => {
                const table = document.querySelector(`[data-table-id="${id}"]`);
                const card = table?.querySelector('.table-card');
                if (!table || !card) return;
                const isInteractive = !card.classList.contains('cursor-not-allowed');
                if (!isInteractive) {
                    selectedIds.delete(id);
                    card.classList.remove('table-selected', 'border-yellow-500', 'bg-yellow-50');
                }
            });
        };

        tables.forEach(table => {
            const card = table.querySelector('.table-card');
            const tableId = table.dataset.tableId;

            table.addEventListener('click', function() {
                if (!card || card.classList.contains('cursor-not-allowed')) {
                    return;
                }

                if (selectedIds.has(tableId)) {
                    selectedIds.delete(tableId);
                    card.classList.remove('table-selected', 'border-yellow-500', 'bg-yellow-50');
                } else {
                    selectedIds.add(tableId);
                    card.classList.add('table-selected', 'border-yellow-500', 'bg-yellow-50');
                }

                syncHiddenInputs();
                updateSummary();
            });
        });

        // Expose helpers for other functions in this script
        window.__reservationSelectedTables = {
            selectedIds,
            syncHiddenInputs,
            updateSummary,
            clearUnavailableSelections,
        };
    }

    function setupGuestChange() {
        const guestsSelect = document.querySelector('select[name="guests"]');
        if (!guestsSelect) return;

        guestsSelect.addEventListener('change', function() {
            if (window.__reservationSelectedTables) {
                window.__reservationSelectedTables.updateSummary();
            }
            loadAvailableTables();
        });
    }

    function setupTimeSlotSelection() {
        const timeSlotsContainer = document.getElementById('timeSlots');
        timeSlotsContainer.addEventListener('click', function(e) {
            if(e.target.classList.contains('time-slot')) {
                // Remove previous selection
                document.querySelectorAll('.time-slot').forEach(el => {
                    el.classList.remove('selected', 'bg-primary-red', 'text-white');
                });

                // Select this time slot
                e.target.classList.add('selected', 'bg-primary-red', 'text-white');
                document.getElementById('selectedTime').value = e.target.dataset.time;
                loadAvailableTables();
            }
        });
    }

    function loadAvailableTimes() {
        const dateInput = document.querySelector('input[name="date"]');
        if(!dateInput || !dateInput.value) {
            console.log('No date input found or empty value');
            return;
        }

        // Show loading
        const timeSlots = document.getElementById('timeSlots');
        if(!timeSlots) {
            console.log('Time slots container not found');
            return;
        }

        timeSlots.innerHTML = `
            <div class="col-span-4 text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-red"></div>
                <p class="mt-2 text-gray-600">Loading available times...</p>
            </div>
        `;

        // Fetch available times
        const guests = document.querySelector('select[name="guests"]')?.value || '';
        const url = '/api/reservation/available-times?date=' + encodeURIComponent(dateInput.value) + '&guests=' + encodeURIComponent(guests);
        console.log('Fetching:', url);

        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if(data.success) {
                    renderTimeSlots(data.times);
                } else {
                    timeSlots.innerHTML = `
                        <div class="col-span-4 text-center py-8">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-2"></i>
                            <p class="text-gray-600">${data.message || 'No times available'}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                timeSlots.innerHTML = `
                    <div class="col-span-4 text-center py-8">
                        <i class="fas fa-exclamation-circle text-red-500 text-3xl mb-2"></i>
                        <p class="text-gray-600">Error loading times. Please try again.</p>
                    </div>
                `;
            });
    }

    function renderTimeSlots(times) {
        const timeSlots = document.getElementById('timeSlots');
        timeSlots.innerHTML = '';

        if(times.length === 0) {
            timeSlots.innerHTML = `
                <div class="col-span-4 text-center py-8">
                    <i class="fas fa-calendar-times text-gray-400 text-3xl mb-2"></i>
                    <p class="text-gray-600">No available times for this date.</p>
                </div>
            `;
            return;
        }

        times.forEach(timeSlot => {
            const timeElement = document.createElement('button');
            timeElement.type = 'button';
            timeElement.className = `time-slot border border-gray-300 rounded-lg py-3 text-center ${timeSlot.available ? 'hover:border-primary-red cursor-pointer' : 'opacity-50 cursor-not-allowed'}`;
            timeElement.textContent = timeSlot.time;
            timeElement.dataset.time = timeSlot.time;

            if(!timeSlot.available) {
                timeElement.title = 'This time slot is fully booked';
                timeElement.disabled = true;
            }

            timeSlots.appendChild(timeElement);
        });
    }

    function loadAvailableTables() {
        const dateVal = document.querySelector('input[name="date"]')?.value;
        const timeVal = document.getElementById('selectedTime')?.value;
        const guestsVal = document.querySelector('select[name="guests"]')?.value;

        if (!dateVal || !timeVal || !guestsVal) {
            return;
        }

        fetch(`/api/tables/available?date=${encodeURIComponent(dateVal)}&time=${encodeURIComponent(timeVal)}&guests=${encodeURIComponent(guestsVal)}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                const availableIds = new Set((data.tables || []).map(t => String(t.id)));
                document.querySelectorAll('.table-item').forEach(table => {
                    const baseAvailable = table.dataset.baseAvailable === '1';
                    const card = table.querySelector('.table-card');
                    const label = table.querySelector('.table-availability-label');
                    const isAvailableNow = baseAvailable && availableIds.has(String(table.dataset.tableId));

                    if (!card || !label) return;

                    if (isAvailableNow) {
                        card.classList.remove('opacity-50', 'cursor-not-allowed');
                        card.classList.add('cursor-pointer');
                        label.textContent = 'Available';
                        label.classList.remove('text-red-600');
                        label.classList.add('text-green-600');
                    } else {
                        card.classList.add('opacity-50', 'cursor-not-allowed');
                        card.classList.remove('cursor-pointer');
                        label.textContent = baseAvailable ? 'Booked' : 'Reserved';
                        label.classList.remove('text-green-600');
                        label.classList.add('text-red-600');
                    }
                });

                if (window.__reservationSelectedTables) {
                    window.__reservationSelectedTables.clearUnavailableSelections();
                    window.__reservationSelectedTables.syncHiddenInputs();
                    window.__reservationSelectedTables.updateSummary();
                }
            })
            .catch(() => {
                // Silent fail: leave base availability UI as-is
            });
    }

    // Form validation
    document.getElementById('reservationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const selectedInputs = document.querySelectorAll('input[name="tables[]"]');
        const selectedTime = document.getElementById('selectedTime').value;
        const guests = document.querySelector('select[name="guests"]').value;

        if(selectedInputs.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Table Required',
                text: 'Please select at least one table for your reservation.',
            });
            return;
        }

        if(!selectedTime) {
            Swal.fire({
                icon: 'error',
                title: 'Time Required',
                text: 'Please select a time for your reservation.',
            });
            return;
        }

        // Validate total capacity
        const selectedTableElements = [...selectedInputs].map(i => document.querySelector(`[data-table-id="${i.value}"]`)).filter(Boolean);
        const totalCapacity = selectedTableElements.reduce((sum, el) => sum + parseInt(el.dataset.capacity || '0'), 0);

        if(parseInt(guests) > totalCapacity) {
            Swal.fire({
                icon: 'error',
                title: 'Capacity Exceeded',
                text: `Selected tables total capacity is ${totalCapacity}. Please select more tables or reduce guest count.`,
            });
            return;
        }

        // Show confirmation
        Swal.fire({
            title: 'Confirm Reservation?',
            html: `
                <div class="text-left">
                    <p><strong>Date:</strong> ${document.querySelector('input[name="date"]').value}</p>
                    <p><strong>Time:</strong> ${selectedTime}</p>
                    <p><strong>Guests:</strong> ${guests}</p>
                    <p><strong>Tables:</strong> ${selectedTableElements.map(el => el.dataset.tableNumber).join(', ')}</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, confirm!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection

