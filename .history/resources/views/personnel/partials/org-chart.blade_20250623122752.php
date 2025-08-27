@foreach($hierarchy as $position => $children)
    <div class="org-level">
        <div class="org-node">
            {{ $position }}
            @php
                $users = $usersByPosition[$position] ?? collect();
            @endphp
            @if($users->count() > 0)
                <div class="text-sm text-gray-600">
                    {{ $users->count() }} {{ __('personnel') }}
                </div>
            @endif
        </div>
        
        @if(is_array($children) && count($children) > 0)
            <div class="org-children">
                @foreach($children as $childPosition => $grandChildren)
                    <div class="org-child">
                        @if(is_array($grandChildren))
                            @include('personnel.partials.org-chart', [
                                'hierarchy' => [$childPosition => $grandChildren],
                                'usersByPosition' => $usersByPosition,
                                'level' => $level + 1
                            ])
                        @else
                            <div class="org-node">
                                {{ $grandChildren }}
                                @php
                                    $users = $usersByPosition[$grandChildren] ?? collect();
                                @endphp
                                @if($users->count() > 0)
                                    <div class="text-sm text-gray-600">
                                        {{ $users->count() }} {{ __('personnel') }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endforeach 