<footer class="text-white text-center text-lg-start fixed-bottom footer-sv">
    <div class="text-center p-2">
        Copyright &copy; {{ date("Y") }}
      <a class="text-white" href="https://salvemundi.nl/" target="_blank">salvemundi.nl</a>
        @if(env('APP_ENV') === 'local' || env('APP_ENV') === 'dev' || env('APP_ENV') === 'development')
            <span class="badge rounded-pill text-bg-danger text-white">DEVELOPMENT</span>
        @endif
    </div>
</footer>
