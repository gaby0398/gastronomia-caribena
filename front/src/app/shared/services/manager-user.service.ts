import { inject, Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { catchError, map, Observable, of, retry, switchMap, tap, throwError } from 'rxjs';
import { IPassw, TypeClient } from '../models/interface';
import { AuthService } from './auth.service';

@Injectable({
  providedIn: 'root'
})
export class ManagerUserService {
  private readonly http = inject(HttpClient);
  private readonly srvAuth = inject(AuthService);
  private readonly SERVER_URL = environment.servidor; // Ejemplo: "http://localhost:9000"
  private readonly ENDPOINT = `${this.SERVER_URL}/api/usuario`;

  httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'application/json',
      //'Authorization': `Bearer ${this.srvAuth.getToken()}`
    })
  };

  /**
   * Obtener información detallada del usuario.
   * @param userParam Alias o correo del usuario.
   */
  getUser(userParam: any): Observable<TypeClient> {
    return this.http.get<TypeClient>(`${this.ENDPOINT}/getUser/${userParam}`).pipe(
      map((userData) => ({
        ...userData // Accedemos al primer elemento del array
      }))/*,
      tap((data) => {
        console.log('Datos del usuario obtenidos:', data);
      })*/,
      retry(1),
      catchError(this.handleError)
    );
  }

  /**
   * Filtrar usuarios por criterios específicos.
   * @param parametros Objeto con los parámetros de filtrado.
   */
  filterUser(parametros: any): Observable<any> {
    let params = new HttpParams();
    for (const prop in parametros) {
      if (parametros.hasOwnProperty(prop)) {
        params = params.append(prop, parametros[prop]);
      }
    }

    return this.http.get<any>(`${this.ENDPOINT}/filtro`, { params }).pipe(
      retry(1),
      catchError(this.handleError)
    );
  }

  /**
   * Crear un nuevo usuario.
   * @param datos Objeto con los datos del nuevo usuario.
   */
  createUser(datos: Partial<TypeClient>): Observable<any> {
    return this.http.post<any>(`${this.ENDPOINT}`, datos, this.httpOptions).pipe(
      retry(1),
      catchError(this.handleError)
    );
  }

  /**
   * Actualizar datos de un usuario existente.
   * @param userParam Alias o correo del usuario.
   * @param datos Objeto con los datos a actualizar.
   */
  updateUser(userParam: any, datos: Partial<TypeClient>): Observable<any> {
    return this.http.patch<any>(`${this.ENDPOINT}/${userParam}`, datos, this.httpOptions).pipe(
      retry(1),
      catchError(this.handleError)
    );
  }

  /**
   * Eliminar un usuario específico.
   * @param userParam Alias o correo del usuario.
   */
  deleteUser(userParam: any): Observable<boolean> {
    return this.http.delete<any>(`${this.ENDPOINT}/${userParam}`, this.httpOptions).pipe(
      retry(1),
      map(() => true),
      catchError(this.handleError)
    );
  }

  /**
   * Cambiar el rol de un usuario.
   * @param param Alias o correo del usuario.
   * @param rol Nuevo rol a asignar.
   */
  changeRol(param: string, rol: number): Observable<any> {
    return this.http.post<any>(`${this.ENDPOINT}/cambiarRol/${param}`, { rol }, this.httpOptions)
      .pipe(
        map(() => true),
        catchError((error) => {
          return of(error.status);
        })
      );
  }

  /**
   * Cambiar la contraseña del usuario actual.
   * @param datos Objeto con la contraseña actual y la nueva.
   */
  savePassw(datos: IPassw): Observable<any> {
    const currentUser = this.srvAuth.valorUsrActual; // Asumiendo que esto contiene idUsuario
    return this.http.post<any>(`${this.ENDPOINT}/${currentUser.alias}/cambiarPassw`, datos, this.httpOptions)
      .pipe(
        map(() => true),
        catchError((error) => {
          return of(error.status);
        })
      );
  }

  /**
   * Resetear la contraseña de un usuario.
   * @param aliasOrCorreo Alias o correo del usuario.
   */
  resetPassw(aliasOrCorreo: string): Observable<any> {
    return this.http.post<any>(`${this.ENDPOINT}/${aliasOrCorreo}/resetearPassw`, {}, this.httpOptions)
      .pipe(
        map(() => true),
        catchError((error) => {
          return of(error.status);
        })
      );
  }

  uploadProfilePhoto(formData: FormData): Observable<any> {
    return this.http.post(`${this.apiUrl}/upload-photo.php`, formData);
  }
  

  /**
   * Manejo centralizado de errores.
   * @param error Objeto de error.
   */
  private handleError(error: any) {
    console.error('Status:' + error.status + ' | Ocurrió un error:', error);
    return throwError(() => new Error('Algo salió mal; por favor intenta nuevamente más tarde.'));
  }

  constructor() { }
}