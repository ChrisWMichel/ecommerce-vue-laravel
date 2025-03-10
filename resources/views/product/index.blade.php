<x-app-layout>
    <div
        class="grid gap-8 p-5 grig-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
      >
      @foreach ($products as $product )
          
      
          <div
            x-data="productItem({
              id: 2, 
              image: 'img/1_1.jpg', 
              title: 'Logitech G502 HERO High Performance Wired Gaming Mouse, HERO 25K Sensor, 25,600 DPI, RGB, Adjustable Weights, 11',
              price: 20
            })"
            class="transition-colors bg-white border border-gray-200 rounded-md border-1 hover:border-purple-600"
          >
            <a href="{{ route('product.view', $product->slug) }}" class="block overflow-hidden aspect-w-3 aspect-h-2">
              <img
                src="{{ $product->image }}"
                alt=""
                class="object-cover transition-transform rounded-lg hover:scale-105 hover:rotate-1"
              />
            </a>
            <div class="p-4">
              <h3 class="text-lg">
                <a href="{{ route('product.view', $product->slug) }}">
                  {{ $product->title }}
                </a>
              </h3>
              @php
              $formattedPrice = number_format($product->price, 2, '.', ',');
            @endphp
              <h5 class="font-bold">${{  $formattedPrice }}</h5>
            </div>
            <div class="flex justify-between px-4 py-3">
              <button
                @click="addToWatchlist()"
                class="flex items-center justify-center w-10 h-10 transition-colors border border-purple-600 rounded-full border-1 hover:bg-purple-600 hover:text-white active:bg-purple-800"
                :class="isInWatchlist(id) ? 'bg-purple-600 text-white' : 'text-purple-600'"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-6 h-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                  />
                </svg>
              </button>
              <button class="btn-primary" @click="addToCart(id)">
                Add to Cart
              </button>
            </div>
          </div>
       @endforeach
     </div>

    <div class="lg:mx-[350px] xl:mx-[500px] md:mx-[0px] sm:mx-[100px] mx-[50px]">
        {{ $products->links() }}
    </div>
</x-app-layout>