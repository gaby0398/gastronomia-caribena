import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Token } from '../models/interface';
import { JwtHelperService } from '@auth0/angular-jwt';

@Injectable({
  providedIn: 'root'
})
export class TokenService {

  private JWT_TOKEN = 'JWT_TOKEN';
  private REFRESH_TOKEN = 'REFRESH_TOKEN';

  private readonly http = inject(HttpClient);

  constructor() { }

  setTokens(tokens: Token): void {
    this.setToken(tokens.token);
    this.setRefreshToken(tokens.tkRef);
  }

  private setToken(token: string): void {
    localStorage.setItem(this.JWT_TOKEN, token);
  }
  private setRefreshToken(token: string): void {
    localStorage.setItem(this.REFRESH_TOKEN, token);
  }

  get token(): any {
    return localStorage.getItem(this.JWT_TOKEN);
  }
  get refreshToken(): any {
    return localStorage.getItem(this.REFRESH_TOKEN);
  }

  eliminarTokens() {
    localStorage.removeItem(this.JWT_TOKEN);
    localStorage.removeItem(this.REFRESH_TOKEN);
  }

  public decodeToken(): any {
    const helper = new JwtHelperService();
    return helper.decodeToken(this.token);
  }

  public jwtTokenExp(): any {
    const helper = new JwtHelperService();
    return helper.isTokenExpired(this.token);
  }

  public tiempoExpToken(): number {
    return this.decodeToken().exp - (Date.now() / 1000);
  }

}
