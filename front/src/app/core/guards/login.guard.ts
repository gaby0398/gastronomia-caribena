import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../../shared/services/auth.service';

export const loginGuard: CanActivateFn = (route, state) => {
  const authSrv = inject(AuthService);
  const router = inject(Router);

  if (authSrv.isLogged()) {
    router.navigate(['']);
  } else {
    authSrv.logout();
  }

  return !authSrv.isLogged();
};
