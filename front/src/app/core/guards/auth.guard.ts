import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../../shared/services/auth.service';
import { inject } from '@angular/core';
import { Role } from '../../shared/models/interface';

export const authGuard: CanActivateFn = (route, state) => {
  const srvAuth = inject(AuthService);
  const router = inject(Router);

  if (srvAuth.isLogged()) {

    if (Object.keys(route.data).length !== 0 && route.data['roles'].indexOf(srvAuth.valorUsrActual.rol) === -1) {
      router.navigate(['/error403']);
      return false;
    }
    return true;

  }
  srvAuth.logout();
  router.navigate(['/login']);
  return false;
};

export const authGuardAdministrador: CanActivateFn = (route, state) => {
  const srvAuth = inject(AuthService);
  const router = inject(Router);

  if (!srvAuth.isLogged()) {
    srvAuth.logout();
    router.navigate(['/login']);
    return false;
  }

  const roles = srvAuth.valorUsrActual.rol;
  if (!(roles === Role.Admin)) {
    router.navigate(['/error403']);
    return false;
  }

  return true;
};
