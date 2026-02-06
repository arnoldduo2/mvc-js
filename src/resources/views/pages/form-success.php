<div class="page-form-success">
   <div class="container">
      <div class="success-card">
         <div class="success-icon">✅</div>
         <h1>Form Submitted Successfully!</h1>

         <?php if ($message ?? null): ?>
            <p class="success-message"><?= htmlspecialchars($message) ?></p>
         <?php endif; ?>

         <?php if (!empty($data ?? [])): ?>
            <div class="validated-data">
               <h2>Validated Data:</h2>
               <table>
                  <?php foreach ($data as $key => $value): ?>
                     <?php if ($key !== 'password' && $key !== 'password_confirmation'): ?>
                        <tr>
                           <th><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) ?>:</th>
                           <td><?= htmlspecialchars((string) $value) ?></td>
                        </tr>
                     <?php endif; ?>
                  <?php endforeach; ?>
               </table>
            </div>
         <?php endif; ?>

         <div class="actions">
            <a href="<?= url('/forms') ?>" class="btn btn-primary">Submit Another Form</a>
            <a href="<?= url('/') ?>" class="btn">Back to Home</a>
         </div>
      </div>
   </div>
</div>

<style>
   .success-card {
      background: var(--bg-secondary);
      padding: 3rem;
      border-radius: 12px;
      text-align: center;
      max-width: 600px;
      margin: 3rem auto;
      box-shadow: 0 4px 12px var(--shadow);
   }

   .success-icon {
      font-size: 4rem;
      margin-bottom: 1rem;
   }

   .success-message {
      font-size: 1.2rem;
      color: var(--accent-color);
      margin: 1rem 0;
   }

   .validated-data {
      margin: 2rem 0;
      text-align: left;
   }

   .validated-data h2 {
      margin-bottom: 1rem;
   }

   .validated-data table {
      width: 100%;
      border-collapse: collapse;
   }

   .validated-data th,
   .validated-data td {
      padding: 0.75rem;
      border-bottom: 1px solid var(--border-color);
      text-align: left;
   }

   .validated-data th {
      font-weight: 600;
      width: 40%;
   }

   .actions {
      display: flex;
      gap: 1rem;
      justify-content: center;
      margin-top: 2rem;
   }
</style>