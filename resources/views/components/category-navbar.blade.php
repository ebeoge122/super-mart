<nav class="category-nav">
  <div class="category-scroll">
    @foreach ($categories as $category)
      <a href="{{ route('category.show', $category->slug) }}"
         class="category-item {{ request()->is('category/'.$category->slug) ? 'active' : '' }}">
        <span class="icon">{{ $category->icon }}</span>
        <span class="label">{{ $category->name }}</span>
      </a>
    @endforeach
  </div>
</nav>