import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { TokenService } from '../../shared/services/token.service';

export const jwtInterceptor: HttpInterceptorFn = (req, next) => {
  const srvToken = inject(TokenService);
  const token = srvToken.token;

  if (token) {
    const clonedReq = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`
      }
    });
    return next(clonedReq);
  }

  return next(req);
};