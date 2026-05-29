 @if (session()->has('message'))
     <div class="w3-panel w3-pale-green w3-leftbar w3-border-green w3-round-large">
         <p class="w3-margin-0">{{ session('message') }}</p>
     </div>
 @endif

 @if ($errors->any())
     <div class="w3-panel w3-pale-red w3-leftbar w3-border-red w3-round-large">
         <ul class="w3-ul" style="margin: 0;">
             @foreach ($errors->all() as $error)
                 <li>{{ $error }}</li>
             @endforeach
         </ul>
     </div>
 @endif
