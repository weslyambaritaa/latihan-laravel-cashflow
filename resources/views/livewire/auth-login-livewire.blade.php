<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">

        {{-- Card Form --}}
        <div class="card shadow-lg border-0 rounded-4 my-5">
            <div class="card-body p-4 p-sm-5">
                
                {{-- Logo dan Judul --}}
                <div class="text-center mb-4">
                    <img src="{{ asset('logo.png') }}" alt="Logo" width="72" height="72" class="mb-3">
                    <h1 class="h3 mb-3 fw-bold">Selamat Datang</h1>
                    <p class="text-muted">Login untuk melanjutkan ke Cashflow App</p>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent="loginUser">
                    
                    {{-- Input Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                placeholder="nama@email.com" 
                                wire:model="email"
                                required>
                        </div>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Input Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                placeholder="Password" 
                                wire:model="password"
                                required>
                        </div>
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tombol Login --}}
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                            <span wire:loading.remove wire:target="loginUser">Login</span>
                            <span wire:loading wire:target="loginUser" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span wire:loading wire:target="loginUser">Loading...</span>
                        </button>
                    </div>

                    {{-- Link ke Register --}}
                    <div class="text-center text-muted">
                        Belum punya akun? <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Daftar di sini</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>