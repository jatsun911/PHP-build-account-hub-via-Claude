@extends('layouts.app')

@section('title', 'Create Entity')

@section('content')
<div class="dashboard-header">
    <h1>Create New Entity</h1>
    <h3>Set up a new business entity to manage its books. (Limit: 3 per plan)</h3>
</div>

<div class="glass-panel" style="max-width: 800px;">
    @if($errors->any())
        <div style="background: hsla(0, 100%, 50%, 0.1); border-left: 4px solid #ef4444; padding: 16px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
            <div style="font-weight: 600; color: #b91c1c;">Please correct the errors below:</div>
            <ul style="color: #b91c1c; margin-top: 8px; font-size: 0.9rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('entities.store') }}" method="POST" onsubmit="return validateForm(event)">
        @csrf
        
        <h3 style="margin-bottom: 16px; color: var(--brand-primary);">Basic Information</h3>
        
        <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">Name of Entity <span style="color:red">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="e.g. Acme Corp">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">Name of Owner <span style="color:red">*</span></label>
                <input type="text" name="owner_name" value="{{ old('owner_name') }}" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="e.g. John Doe">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">Email Address <span style="color:red">*</span></label>
                <div style="display: flex; gap: 8px;">
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required style="flex: 1; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    <button type="button" id="btn_send_email_otp" onclick="sendOtp('email')" style="background: #e2e8f0; color: #334155; border: none; padding: 0 16px; border-radius: 8px; font-weight: 600; cursor: pointer;">Send OTP</button>
                </div>
                <div id="email_otp_section" style="display: none; margin-top: 8px; gap: 8px;">
                    <input type="text" id="email_otp" placeholder="Enter OTP" style="flex: 1; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    <button type="button" onclick="verifyOtp('email')" style="background: var(--brand-primary); color: white; border: none; padding: 0 16px; border-radius: 8px; font-weight: 600; cursor: pointer;">Verify</button>
                </div>
                <div id="email_status" style="margin-top: 4px; font-size: 0.8rem; color: #10b981; font-weight: 500;"></div>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">Mobile Number <span style="color:red">*</span> (Verification Optional)</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}" required style="flex: 1; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    <button type="button" id="btn_send_mobile_otp" onclick="sendOtp('mobile')" style="background: #e2e8f0; color: #334155; border: none; padding: 0 16px; border-radius: 8px; font-weight: 600; cursor: pointer;">Send OTP</button>
                </div>
                <div id="mobile_otp_section" style="display: none; margin-top: 8px; gap: 8px;">
                    <input type="text" id="mobile_otp" placeholder="Enter OTP" style="flex: 1; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    <button type="button" onclick="verifyOtp('mobile')" style="background: var(--brand-primary); color: white; border: none; padding: 0 16px; border-radius: 8px; font-weight: 600; cursor: pointer;">Verify</button>
                </div>
                <div id="mobile_status" style="margin-top: 4px; font-size: 0.8rem; color: #10b981; font-weight: 500;"></div>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">Constitution of Entity <span style="color:red">*</span></label>
                <select name="constitution" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: white;">
                    <option value="">Select Constitution...</option>
                    <option value="Proprietorship">Proprietorship</option>
                    <option value="Partnership">Partnership</option>
                    <option value="LLP">LLP</option>
                    <option value="Company">Company</option>
                    <option value="AOP">AOP</option>
                    <option value="Trust">Trust</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">Nature of Business <span style="color:red">*</span></label>
                <select name="nature_of_business" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: white;">
                    <option value="">Select Nature...</option>
                    <option value="Service">Service</option>
                    <option value="Trading">Trading</option>
                    <option value="Manufacturing">Manufacturing</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">Financial Year Starting From <span style="color:red">*</span></label>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="color: var(--text-secondary); font-weight: 500; background: #f1f5f9; padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color);">1st April</span>
                    <input type="number" name="accounting_period_year" value="{{ old('accounting_period_year', 2016) }}" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                </div>
            </div>
        </div>
        <h3 style="margin-bottom: 16px; color: var(--brand-primary); border-top: 1px solid var(--glass-border); padding-top: 24px;">Registration Details</h3>

        <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">PAN</label>
                <input type="text" name="pan" value="{{ old('pan') }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="Optional">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">GSTIN</label>
                <input type="text" name="gstin" value="{{ old('gstin') }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;" placeholder="Optional">
            </div>
            
            <div style="grid-column: span 2; display: flex; align-items: center; gap: 8px; margin-top: 8px;">
                <input type="checkbox" name="is_msme" id="is_msme" value="1" onchange="toggleMSME()" style="width: 18px; height: 18px;">
                <label for="is_msme" style="font-weight: 500; font-size: 0.9rem;">Is MSME Registered?</label>
            </div>
            
            <div id="msme_details" style="display: none; grid-column: span 2; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 8px;">
                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">MSME Number</label>
                    <input type="text" name="msme_no" value="{{ old('msme_no') }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">MSME Registration Date</label>
                    <input type="date" name="msme_date" value="{{ old('msme_date') }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                </div>
            </div>
            
            <div style="grid-column: span 2;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">Registered Address</label>
                <textarea name="address" rows="3" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">{{ old('address') }}</textarea>
            </div>
        </div>

        <button type="submit" style="background: var(--brand-primary); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save & Create Entity
        </button>
    </form>
</div>

<script>
    let isEmailVerified = false;

    function toggleMSME() {
        const checkbox = document.getElementById('is_msme');
        const details = document.getElementById('msme_details');
        details.style.display = checkbox.checked ? 'grid' : 'none';
    }
    
    async function sendOtp(type) {
        const inputVal = document.getElementById(type).value;
        if (!inputVal) return alert('Please enter ' + type + ' first.');
        
        try {
            const response = await fetch('/api/otp/send-' + type, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ [type]: inputVal })
            });
            const data = await response.json();
            if (data.status === 'success') {
                document.getElementById(type + '_otp_section').style.display = 'flex';
                document.getElementById('btn_send_' + type + '_otp').innerText = 'Resend';
            } else {
                alert(data.message || 'Error sending OTP');
            }
        } catch (e) { console.error(e); alert('Error sending OTP'); }
    }

    async function verifyOtp(type) {
        const inputVal = document.getElementById(type).value;
        const otpVal = document.getElementById(type + '_otp').value;
        if (!otpVal) return alert('Please enter OTP.');
        
        try {
            const response = await fetch('/api/otp/verify-' + type, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ [type]: inputVal, otp: otpVal })
            });
            const data = await response.json();
            if (data.status === 'success') {
                document.getElementById(type + '_otp_section').style.display = 'none';
                document.getElementById('btn_send_' + type + '_otp').style.display = 'none';
                document.getElementById(type).readOnly = true;
                document.getElementById(type + '_status').innerText = '✓ Verified';
                if (type === 'email') isEmailVerified = true;
            } else {
                alert(data.message || 'Invalid OTP');
            }
        } catch (e) { console.error(e); alert('Error verifying OTP'); }
    }

    function validateForm(e) {
        if (!isEmailVerified) {
            e.preventDefault();
            alert('Please verify your Email Address before creating an entity.');
            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', toggleMSME);
</script>
@endsection
