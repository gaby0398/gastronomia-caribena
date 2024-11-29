import { inject, Injectable } from '@angular/core';
import { TokenService } from './token.service';
import { Router } from '@angular/router';
import { BehaviorSubject, catchError, map, Observable, of, retry, tap } from 'rxjs';
import { Token, User } from '../models/interface';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';

const _SERVER = environment.servidor;
const LIMITE_REFRESH = 20;
@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private srvToken = inject(TokenService);
  private router = inject(Router);

  private usrActualSubject = new BehaviorSubject<User>(new User());
  public usrActual = this.usrActualSubject.asObservable();
  private refrescando = false;

  private readonly http = inject(HttpClient);
  httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'Application/json'
    })
  };

  constructor() { }

  public get valorUsrActual(): User { 
    return this.usrActualSubject.value;
  }

  public login(datos: { usuario: string, passw: string }): Observable<any> {
    return this.http
      .post<any>(`${_SERVER}/api/auth/iniciar`, datos, this.httpOptions) // Incluir httpOptions
      .pipe(
        retry(1),
        tap((tokens) => {
          this.doLogin(tokens);
          // Remover la navegación aquí para evitar duplicaciones
        }),
        map(() => true),
        catchError((error) => {
          return of(error.status)
        })
      );
  }

  public logout() {
    if (this.isLogged()) {
      this.http
        .patch(`${_SERVER}/api/auth/cerrar/${this.valorUsrActual.alias}`, {})  //Listo
        .subscribe();
      this.doLogout();
    }
  }

  private doLogin(tokens: Token) {
    this.srvToken.setTokens(tokens);
    /// compartir datos de sesión a toda la aplicación
    this.usrActualSubject.next(this.getUserActual());
    //console.log(this.valorUsrActual);
  }

  private doLogout() {
    if (this.srvToken.token) {
      this.srvToken.eliminarTokens();
    }
    // limpiar los datos compartidos de usuario
    this.usrActualSubject.next(this.getUserActual());
    this.router.navigate(['/login']);
  }

  public isLogged(): boolean {
    return !!this.srvToken.token && !this.srvToken.jwtTokenExp();
  }

  getUserActual(): User {
    if (!this.srvToken.token) {
      return new User();
    }
    const tokenD = this.srvToken.decodeToken();
    return { alias: tokenD.sub, nombre: tokenD.nom, rol: tokenD.rol }
  }

  public refreshAuth() {
    if (!this.refrescando) {
      this.refrescando = true;
      return this.http.patch<Token>(`${environment.servidor}/api/auth/refrescar`,
        {
          idUsuario: (this.srvToken.decodeToken().sub),
          tkRef: this.srvToken.refreshToken
        })
        .subscribe(
          tokens => {
            this.srvToken.setTokens(tokens);
            this.refrescando = false
          }
        )
    }
    return false;
  }
  
  public verificarRefresh(): boolean {
    if (this.isLogged()) {
      const tiempo = this.srvToken.tiempoExpToken();
      if (tiempo <= 0) {
        this.logout();
        return false;
      }
      if (tiempo > 0 && tiempo <= LIMITE_REFRESH) {
        this.refreshAuth();
      }
      return true;

    } else {
      this.logout();
      return false;
    }
  }
}