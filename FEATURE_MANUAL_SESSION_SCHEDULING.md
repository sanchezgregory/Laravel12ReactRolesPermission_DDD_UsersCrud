# Feature: Manual Session Scheduling After Calendly Booking

## 📋 Overview
This feature allows users to manually register their Calendly-scheduled session date/time in the system after booking, which then notifies the mediator via email for manual verification.

## 🔄 User Flow

1. **User pays for session** → Session status = `paid`, `scheduled_at` = `null`
2. **User clicks "Agendar Sesión"** → Opens Calendly in new tab
3. **User books in Calendly** (separate tab, no automatic redirect)
4. **User returns to mediator page** → Sees "Ya agendé mi sesión" button
5. **User clicks button** → Modal opens with form
6. **User enters date/time + optional notes** → Submits form
7. **System saves `scheduled_at`** and sends email to mediator
8. **Mediator receives email** → Manually verifies in Calendly
9. **Mediator confirms** from their panel (future feature)

## 🗄️ Database Changes

### Migration: `add_scheduled_at_to_session_payments_table`
```php
$table->dateTime('scheduled_at')->nullable()->after('metadata');
```

### Model: `SessionPayment`
- Added `scheduled_at` to `$fillable`
- Added `scheduled_at` to `$casts` as `datetime`

## 🎯 Backend Implementation

### Controller: `SubmitScheduledSessionController`
**Route:** `POST /payments/submit-schedule`  
**Name:** `payments.submit-schedule`

**Validation:**
- `mediator_id`: required, integer, exists in users table
- `scheduled_at`: required, valid datetime
- `notes`: optional, string, max 500 chars

**Logic:**
1. Finds paid session for user+mediator where `scheduled_at IS NULL`
2. Updates `scheduled_at` and stores notes in `metadata`
3. Sends email to mediator with session details
4. Returns success message

### Email: `SessionScheduledConfirmationMail`
**Subject:** "Nueva Sesión Agendada - Confirmación Pendiente"

**Data passed:**
- Mediator name
- User name & email
- Scheduled date/time
- Optional notes
- Calendly URL (if available)

**Template:** `resources/views/emails/session-scheduled-confirmation.blade.php`

### Repository Update: `SessionPaymentEloquentRepository`
**Method:** `hasActivePayment()`
- Now checks for `paid` sessions **without** `scheduled_at`
- This ensures users can only schedule once per payment

## 🎨 Frontend Implementation

### Component: `mediators/Show.tsx`

**New State:**
```tsx
const [showScheduleModal, setShowScheduleModal] = useState(false);
const { data, setData, post, processing, errors, reset } = useForm({
    mediator_id: mediator.id,
    scheduled_at: '',
    notes: '',
});
```

**New UI Elements:**
1. **"Ya agendé mi sesión" button** (appears when `has_active_payment === true`)
2. **Schedule submission modal** with:
   - `datetime-local` input for date/time
   - Textarea for optional notes (max 500 chars)
   - Character counter
   - Cancel/Submit buttons

**Form Submission:**
```tsx
post(route('payments.submit-schedule'), {
    onSuccess: () => {
        setShowScheduleModal(false);
        reset();
    },
});
```

## 📧 Email Template Features

- **Professional design** with branded colors
- **Clear information hierarchy**
- **Action required alert box**
- **Direct link to Calendly** (if URL exists)
- **Formatted date/time** (dd/mm/yyyy HH:mm)
- **Responsive layout**

## 🔒 Security & Validation

✅ **Authentication required** (route protected by `auth:sanctum`)  
✅ **User can only schedule their own sessions**  
✅ **Prevents double-scheduling** (`whereNull('scheduled_at')`)  
✅ **Input validation** (date format, max length)  
✅ **CSRF protection** (Laravel default)

## 🚀 To Deploy

1. **Run migration:**
   ```bash
   php artisan migrate
   ```

2. **Clear caches:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Test email sending** (configure `.env` mail settings)

## 📝 Future Enhancements

- [ ] Mediator confirmation panel
- [ ] Automatic Calendly webhook integration
- [ ] Session reminder emails
- [ ] Reschedule functionality
- [ ] Calendar export (ICS file)

## 🐛 Known Limitations

- **No automatic Calendly sync** - relies on user manual input
- **No validation against Calendly** - mediator must verify manually
- **One-time scheduling** - once set, user cannot modify (locked)

## 📂 Files Modified/Created

### Created:
- `app/Src/Infrastructure/Controllers/Payments/SubmitScheduledSessionController.php`
- `app/Mail/SessionScheduledConfirmationMail.php`
- `resources/views/emails/session-scheduled-confirmation.blade.php`
- `database/migrations/2026_01_17_222338_add_scheduled_at_to_session_payments_table.php`

### Modified:
- `app/Models/SessionPayment.php`
- `routes/payments_routes.php`
- `resources/js/Pages/mediators/Show.tsx`
- `app/Src/Infrastructure/Repositories/Eloquent/SessionPaymentEloquentRepository.php`
- `app/Src/Infrastructure/Controllers/Mediators/ShowController.php`

### Mediator Backoffice Features

**Route:** `/backoffice/my-sessions`

1. **View Scheduled Sessions:**
   - Shows list of sessions with status and scheduled date/time.
   - Displays "Pending Schedule" if not yet booked by user.

2. **Confirm Session:**
   - Button "Confirmar" updates metadata `confirmed_by_mediator = true`.

3. **Reschedule Session:**
   - Button "Editar" opens modal to change date/time.
   - **Controller:** `UpdateSessionScheduleController`
   - **Email:** `SessionRescheduledNotificationMail` sends notification to client with old and new date.

### Additional Files Created:
- `app/Src/Infrastructure/Controllers/Backoffice/MediatorSpace/ConfirmSessionController.php`
- `app/Src/Infrastructure/Controllers/Backoffice/MediatorSpace/UpdateSessionScheduleController.php`
- `app/Mail/SessionRescheduledNotificationMail.php`
- `resources/views/emails/session-rescheduled-notification.blade.php`

### User Space Features

**Route:** `/my-sessions` (Named: `user.sessions`)

1. **View My Sessions:**
   - Accessible via **User Dropdown** > "Mis Sesiones" or **Alert Banner**.
   - Lists all sessions (history) for the logged-in user.
   - Shows Mediator name, status, and scheduled date.
   - "Agendar Ahora" button if session is Paid but not scheduled.

2. **Alert Banner Logic Update:**
   - Now only shows "Pending/Active" sessions if:
     - `scheduled_at` is NULL (pending)
     - OR `scheduled_at` is in the future.
   - Past sessions do not trigger the alert.

### Additional Files Created:
- `app/Src/Infrastructure/Controllers/Backoffice/UserSpace/MySessionsController.php`
- `resources/js/Pages/backoffice/user-space/my-sessions.tsx`

---

**Status:** ✅ Ready for testing (remember to run `sail artisan migrate`)
