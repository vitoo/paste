@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create a New Paste</div>
                <div class="card-body">
                    <form action="{{ route('pastes.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="content" class="form-label">Paste Content :</label>
                            <textarea name="content" id="content" rows="15" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="expiration" class="form-label">Expiration :</label>
                            <select name="expiration" id="expiration" class="form-select" required>
                                <option value="1day">1 Day</option>
                                <option value="1week">1 Week</option>
                                <option value="1month">1 Month</option>
                                <option value="1year" selected>1 Year</option>
                                <option value="never">Never</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Paste</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection