import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../../shared/services/auth.service';
import { inject } from '@angular/core';
import { Role } from '../../shared/models/interface';
import Swal from 'sweetalert2';

export const authGuard: CanActivateFn = (route, state) => {
  const srvAuth = inject(AuthService);
  const router = inject(Router);

  if (srvAuth.isLogged()) {
    const roles = route.data['roles'] as Role[];
    if (roles && roles.length > 0) {
      if (roles.indexOf(srvAuth.valorUsrActual.rol) === -1) {
        // Rol no autorizado
        Swal.fire({
          title: 'Acceso Denegado',
          text: 'No tienes permisos para acceder a esta sección.',
          icon: 'error',
          confirmButtonText: 'Aceptar'
        });
        router.navigate(['/login']);
        return false;
      }
    }
    return true;
  }

  // No está autenticado
  router.navigate(['/login']);
  return false;
};
