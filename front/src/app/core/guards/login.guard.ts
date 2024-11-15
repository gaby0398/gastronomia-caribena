import { inject } from '@angular/core';
import { CanActivateFn } from '@angular/router';
import { AuthService } from '../../shared/services/auth.service';

export const loginGuard: CanActivateFn = (route, state) => {
  const authSrv = inject(AuthService);
  if (authSrv.isLogged()) {
  } else {
    authSrv.logout();
  }
  return !authSrv.isLogged();
};
