import 'package:flutter/material.dart';

import '../../data/model/user_model.dart';
import '../../shared/utils/utils.dart';
import 'home/home_screen.dart';
import 'myitems/my_items_screen.dart';
import 'profile/profile_screen.dart';
import 'report/report_selection_screen.dart';

class MainNavigationScreen extends StatefulWidget {
  final UserModel currentUser;
  final int initialTabIndex;
  final int? myItemsSubTabIndex; // 0 = Laporan, 1 = Klaim Saya

  const MainNavigationScreen({
    super.key,
    required this.currentUser,
    this.initialTabIndex = 0,
    this.myItemsSubTabIndex,
  });

  @override
  State<MainNavigationScreen> createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> {
  int _currentIndex = 0;
  late UserModel _currentUser;

  final GlobalKey<HomeScreenState> _homeKey = GlobalKey<HomeScreenState>();
  final GlobalKey<MyItemsScreenState> _myItemsKey =
      GlobalKey<MyItemsScreenState>();
  final GlobalKey<ProfileScreenState> _profileKey =
      GlobalKey<ProfileScreenState>();

  @override
  void initState() {
    super.initState();
    _currentUser = widget.currentUser;
    _currentIndex = widget.initialTabIndex;
  }

  void _onTabTapped(int index) {
    if (index == 0 && _currentIndex != 0) {
      _homeKey.currentState?.refreshData();
    }
    if (index == 2 && _currentIndex != 2) {
      _myItemsKey.currentState?.refreshData();
    }
    setState(() => _currentIndex = index);
  }

  void _updateUser(UserModel updatedUser) {
    setState(() {
      _currentUser = updatedUser;
    });
  }

  @override
  Widget build(BuildContext context) {
    // Build screens list inside build to get latest _currentUser
    final screens = [
      HomeScreen(key: _homeKey, currentUser: _currentUser),
      const ReportSelectionScreen(),
      MyItemsScreen(
        key: _myItemsKey,
        initialMainTabIndex: widget.myItemsSubTabIndex ?? 0,
      ),
      ProfileScreen(
        key: _profileKey,
        currentUser: _currentUser,
        onUserUpdated: _updateUser,
      ),
    ];

    return Scaffold(
      body: IndexedStack(index: _currentIndex, children: screens),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: _onTabTapped,
        type: BottomNavigationBarType.fixed,
        backgroundColor: AppColors.surface,
        selectedItemColor: AppColors.primary,
        unselectedItemColor: AppColors.textSecondary,
        selectedFontSize: 12,
        unselectedFontSize: 12,
        elevation: 8,
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.home_outlined),
            activeIcon: Icon(Icons.home),
            label: 'Home',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.add_box_outlined),
            activeIcon: Icon(Icons.add_box),
            label: 'Lapor',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.inventory_2_outlined),
            activeIcon: Icon(Icons.inventory_2),
            label: 'MyItem',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person_outline),
            activeIcon: Icon(Icons.person),
            label: 'Profile',
          ),
        ],
      ),
    );
  }
}
