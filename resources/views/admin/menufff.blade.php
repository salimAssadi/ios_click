@php
    $menu = config('menu'); // Load menu configuration
@endphp

<div id="main-menu">
    <ul>
        @foreach ($menu as $item)
            @php
                $isVisible = true;

                // Check permissions
                if (!empty($item['permissions'])) {
                    $isVisible = false;
                    foreach ($item['permissions'] as $permission) {
                        if (Gate::check($permission)) {
                            $isVisible = true;
                            break;
                        }
                    }
                }

                // Check custom conditions
                if (isset($item['condition']) && is_string($item['condition'])) {
                    $isVisible = call_user_func($item['condition']); // Call helper function
                }
            @endphp

            @if ($isVisible)
                <li>
                    <a href="{{ $item['route'] ? route($item['route']) : '#' }}" 
                       class="{{ $item['replace_content'] ? 'replace-content' : '' }}"
                       data-show-back="{{ $item['show_back_button'] ? 'true' : 'false' }}">
                        <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                    </a>

                    @if (!empty($item['submenu']))
                        <ul class="submenu">
                            @foreach ($item['submenu'] as $subItem)
                                @php
                                    $isSubVisible = true;

                                    // Check submenu permissions
                                    if (!empty($subItem['permissions'])) {
                                        $isSubVisible = false;
                                        foreach ($subItem['permissions'] as $permission) {
                                            if (Gate::check($permission)) {
                                                $isSubVisible = true;
                                                break;
                                            }
                                        }
                                    }

                                    // Check submenu conditions
                                    if (isset($subItem['condition']) && is_string($subItem['condition'])) {
                                        $isSubVisible = call_user_func($subItem['condition']); // Call helper function
                                    }
                                @endphp

                                @if ($isSubVisible)
                                    <li>
                                        <a href="{{ route($subItem['route']) }}">
                                            <i class="{{ $subItem['icon'] }}"></i> {{ $subItem['label'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
</div>

<!-- Back Button -->
<button id="back-button" style="display: none;" onclick="goBack()">Back</button>

<script>
    // Handle menu item clicks
    document.querySelectorAll('.replace-content').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const showBack = this.dataset.showBack === 'true';
            const url = this.getAttribute('href');

            // Replace content dynamically (e.g., via AJAX or iframe)
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('main-menu').innerHTML = html;

                    // Show back button if needed
                    const backButton = document.getElementById('back-button');
                    backButton.style.display = showBack ? 'block' : 'none';
                });
        });
    });

    // Back button functionality
    function goBack() {
        window.history.back();
    }
</script>