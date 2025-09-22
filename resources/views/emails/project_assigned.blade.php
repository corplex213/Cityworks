<x-guest-layout>
    <div style="max-width: 480px; margin: 32px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); font-family: Arial, sans-serif; overflow: hidden;">
        <div style="background: #1e293b; padding: 24px 0; text-align: center;">
            <h2 style="color: #fff; margin: 0; font-size: 1.25rem;">CEO Planning and Construction Division</h2>
        </div>
        <div style="padding: 32px 24px 24px 24px;">
            <p style="color: #334155; font-size: 1rem; margin-bottom: 16px;">
                Hello <strong>{{ $user->name }}</strong>,
            </p>
            <p style="color: #334155; font-size: 1rem; margin-bottom: 16px;">
                You have been assigned to an activity:
                <span style="font-weight: bold; color: #0ea5e9;">{{ $project->name }}</span>
            </p>
            <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 24px;">
                Please log in to your account for more details.
            </p>
        </div>
        <div style="background: #f1f5f9; color: #64748b; font-size: 0.85rem; text-align: center; padding: 16px;">
            &copy; {{ date('Y') }} CEO Planning and Construction Division. All rights reserved.
        </div>
    </div>
</x-guest-layout>