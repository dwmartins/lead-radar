<section class="not-found-view container item-center vh-100">
    <div class="text-center">
        <img src="{{ asset('images/svg/error-500.svg') }}" alt="not-found" class="mb-3"> 
        <h2>Ops, ocorreu um erro.</h2>
        <p>Já estamos trabalhando nisso!</p>
    </div>
</section>

<style>
    img {
        width: 100%;
        max-width: 450px;
    }
</style>

@vite([
    'resources/js/app.js',
    'resources/css/app.css',
    'resources/css/notyf-theme.css'
])