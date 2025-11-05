<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">

        {{-- Card Form --}}
        <div class="card shadow-lg border-0 rounded-4 my-5">
            <div class="card-body p-4 p-sm-5">
                
                {{-- Logo dan Judul --}}
                <div class="text-center mb-4">
                    <img src="{{ asset('logo.png') }}" alt="Logo" width="72" height="72" class="mb-3">
                    <h1 class="h3 mb-3 fw-bold">Buat Akun Baru</h1>
                    <p class="text-muted">Isi data di bawah untuk mendaftar</p>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent="registerUser">
                    
                    {{-- Input Nama --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                placeholder="Nama Anda" 
                                wire:model="name"
                                required>
                        </div>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

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

                    {{-- Input Konfirmasi Password --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-check-circle-fill"></i></span>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password_confirmation" 
                                placeholder="Ulangi Password" 
                                wire:model="password_confirmation"
                                required>
                        </div>
                    </div>

                    {{-- Tombol Register --}}
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold">
                            <span wire:loading.remove wire:target="registerUser">Daftar</span>
                            <span wire:loading wire:target="registerUser" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span wire:loading wire:target="registerUser">Mendaftar...</span>
                        </button>
                    </div>

                    {{-- Link ke Login --}}
                    <div class="text-center text-muted">
                        Sudah punya akun? <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Login di sini</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>