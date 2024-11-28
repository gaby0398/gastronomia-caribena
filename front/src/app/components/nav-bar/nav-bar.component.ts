import { Component, signal, Input, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../shared/services/auth.service';
import { Observable } from 'rxjs';
import { User } from '../../shared/models/interface';
import { HamburgerMenuComponent } from '../hamburger-menu/hamburger-menu.component';

type MenuItem = {
  icon: string;
  label: string;
  route: string;
};

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, MatIconModule, RouterModule, HamburgerMenuComponent],
  templateUrl: './nav-bar.component.html',
  styleUrls: ['./nav-bar.component.scss'],
})
export class NavbarComponent implements OnInit {
  usuario$!: Observable<User>;
  menuItems = signal<MenuItem[]>([    
  ]);

  srvAuth = inject(AuthService);

  ngOnInit(): void {
    this.usuario$ = this.srvAuth.usrActual;
  }
}
