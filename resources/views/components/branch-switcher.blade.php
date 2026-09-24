@if(auth()->check() && auth()->user()->hasRole(['salon_admin', 'super_admin']))
    @php
        $salon = auth()->user()->salon;
        $currentBranch = app()->has('current_branch') ? app('current_branch') : null;
        $branches = $salon ? $salon->branches()->active()->get() : collect();
    @endphp

    @if($branches->count() > 1)
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="branchSwitcher" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-building me-1"></i>
                {{ $currentBranch ? $currentBranch->name : 'Select Branch' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="branchSwitcher">
                @foreach($branches as $branch)
                    <li>
                        <form action="{{ route('admin.branches.switch') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                            <button type="submit" class="dropdown-item {{ $currentBranch && $currentBranch->id === $branch->id ? 'active' : '' }}">
                                <i class="fas fa-check me-2 {{ $currentBranch && $currentBranch->id === $branch->id ? '' : 'invisible' }}"></i>
                                {{ $branch->name }}
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endif
