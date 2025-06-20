<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <h1>Create Order</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="contract_number">Contract Number *</label>
            <input type="text" id="contract_number" name="contract_number" value="{{ old('contract_number') }}" required>
        </div>
        
        <div class="form-group">
            <label for="bl_number">BL Number *</label>
            <input type="text" id="bl_number" name="bl_number" value="{{ old('bl_number') }}" required>
        </div>
        
        <div class="form-group">
            <label for="freight_payer_self">Freight Payer *</label>
            <select id="freight_payer_self" name="freight_payer_self" required>
                <option value="">Select...</option>
                <option value="1" {{ old('freight_payer_self') == '1' ? 'selected' : '' }}>Self</option>
                <option value="0" {{ old('freight_payer_self') == '0' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="bl_release_date">BL Release Date</label>
            <input type="datetime-local" id="bl_release_date" name="bl_release_date" value="{{ old('bl_release_date') }}">
        </div>
        
        <div class="form-group">
            <label for="bl_release_user_id">BL Release User ID</label>
            <input type="number" id="bl_release_user_id" name="bl_release_user_id" value="{{ old('bl_release_user_id') }}">
        </div>
        
        <button type="submit">Create Order</button>
    </form>
</body>
</html> 