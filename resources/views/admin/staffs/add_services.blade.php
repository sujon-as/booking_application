@extends('admin_master')
@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="card card-info">
                <div class="card-header">
                    <h3>Add Services for {{ $staff->name }}</h3>
                </div>

                <form action="{{ route('staffs.store.services', $staff->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body" id="service-wrapper">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="branch_id">Branch <span class="required">*</span></label>
                                    <select class="form-control select2bs4" name="branch_id" id="branch_id" required="">
                                        <option value="" selected="" disabled="">Select One</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="specialty_id">Specialty <span class="required">*</span></label>
                                    <select class="form-control select2bs4" name="specialty_id" id="specialty_id" required="">
                                        <option value="" selected="" disabled="">Select One</option>
                                        @foreach($specialities as $speciality)
                                            <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('specialty_id')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="experience_id">Experience <span class="required">*</span></label>
                                    <select class="form-control select2bs4" name="experience_id" id="experience_id" required="">
                                        <option value="" selected="" disabled="">Select One</option>
                                        @foreach($experiences as $experience)
                                            <option value="{{ $experience->id }}">{{ $experience->year_of_exp . ' Years' }}</option>
                                        @endforeach
                                    </select>
                                    @error('experience_id')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="working_day_id">Working Days <span class="required">*</span></label>
                                    <select class="form-control select2bs4" name="working_day_id" id="working_day_id" required="">
                                        <option value="" selected="" disabled="">Select One</option>
                                        @foreach($workingDays as $workingDay)
                                            <option value="{{ $workingDay->id }}">{{ $workingDay->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('working_day_id')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="working_time_range_id">Time (From To) <span class="required">*</span></label>
                                    <select class="form-control select2bs4" name="working_time_range_id" id="working_time_range_id" required="">
                                        <option value="" selected="" disabled="">Select One</option>
                                        @foreach($workingTimeRanges as $workingTimeRange)
                                            <option value="{{ $workingTimeRange->id }}">
                                                {{ timeFormat($workingTimeRange->from_time) . ' to ' . timeFormat($workingTimeRange->to_time) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('working_time_range_id')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- প্রথম row (এটি সবসময় থাকবে + button সহ) -->
                        <div class="row service-row mb-3">
                            <div class="col-md-4">
                                <select name="service_id[]" class="form-control select2bs4" required>
                                    <option value="">Select service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="duration" class="form-control select2bs4" required>
                                    <option value="">Select duration</option>
                                    @foreach($durations as $duration)
                                        <option value="{{ $duration->id }}">{{ $duration->time_duration . ' ' . $duration->time_unit }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <input type="number" step="0.01" name="price[]" class="form-control"
                                       placeholder="Price" required>
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-success add-row">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Services
                        </button>
                        <a href="{{ route('staffs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>

                </form>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Add Row functionality
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-row') || e.target.closest('.add-row')) {
                    addNewRow();
                }
            });

            // Remove Row functionality
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
                    removeRow(e);
                }
            });

            // Add new row function
            function addNewRow() {
                // প্রথম row টা clone করুন
                let firstRow = document.querySelector('.service-row');
                let newRow = firstRow.cloneNode(true);

                // সব input field খালি করুন
                newRow.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });

                // Select এর value reset করুন
                newRow.querySelectorAll('select').forEach(select => {
                    select.selectedIndex = 0;
                });

                // + button কে - button এ পরিবর্তন করুন
                let buttonCol = newRow.querySelector('.col-md-2');
                buttonCol.innerHTML = `
            <button type="button" class="btn btn-danger remove-row">
                <i class="fas fa-minus"></i> Remove
            </button>
        `;

                // নতুন row যোগ করুন
                document.getElementById('service-wrapper').appendChild(newRow);

                // Animation (optional)
                newRow.style.opacity = '0';
                setTimeout(() => {
                    newRow.style.transition = 'opacity 0.3s';
                    newRow.style.opacity = '1';
                }, 10);
            }

            // Remove row function
            function removeRow(e) {
                let rows = document.querySelectorAll('.service-row');

                // অন্তত একটি row থাকতে হবে
                if (rows.length <= 1) {
                    alert('At least one service must be added!');
                    return;
                }

                // Confirmation (optional)
                if (confirm('Are you sure you want to remove this service?')) {
                    let row = e.target.closest('.service-row');

                    // Animation করে remove করুন
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                    }, 300);
                }
            }

            // Prevent duplicate service selection (optional)
            document.addEventListener('change', function(e) {
                if (e.target.name === 'service_id[]') {
                    checkDuplicateServices();
                }
            });

            function checkDuplicateServices() {
                let selectedServices = [];
                let selects = document.querySelectorAll('select[name="service_id[]"]');

                selects.forEach(select => {
                    if (select.value) {
                        if (selectedServices.includes(select.value)) {
                            alert('This service is already selected!');
                            select.value = '';
                            select.focus();
                        } else {
                            selectedServices.push(select.value);
                        }
                    }
                });
            }
        });
    </script>

    <style>
        .service-row {
            margin-bottom: 15px;
        }

        .service-row:last-child {
            margin-bottom: 0;
        }
    </style>

@endsection
